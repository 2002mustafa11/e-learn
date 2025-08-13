import socket
import json
import threading
import requests
import nest_asyncio
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from openai import OpenAI
from pyngrok import ngrok, conf
import uvicorn

# تفعيل async في بيئات مثل Google Colab
nest_asyncio.apply()

# إعداد ngrok مع التوكن الخاص بك
conf.get_default().auth_token = "30mq11coJsgLP3bNXZ7ig3czFUJ_5X53izvRTwuvGAHz5V3Jt"

# إنشاء كائن OpenAI للاتصال بـ OpenRouter API
client = OpenAI(
    base_url="https://openrouter.ai/api/v1",
    api_key="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"
)

GOOGLE_SCRIPT_URL = "https://script.google.com/macros/s/AKfycbybdFsVNr36AjOzQtbDmO-DCckD57D0s4wEb9MUvEDRO9ZnQeK8HI1uthNMVGB5LB0_4A/exec"

app = FastAPI()

class GradeInput(BaseModel):
    question: str
    model_answer: str
    student_answer: str

    class Config:
        schema_extra = {
            "example": {
                "question": "What is photosynthesis?",
                "model_answer": "Photosynthesis is the process by which plants convert light into energy.",
                "student_answer": "It’s how plants make food from light."
            }
        }

class GradeOutput(BaseModel):
    score: int
    feedback: str

def evaluate_logic(question: str, model_answer: str, student_answer: str) -> dict:
    prompt = f"""
قيّم إجابة الطالب مقارنة بالإجابة النموذجية.

السؤال: {question}
الإجابة النموذجية: {model_answer}
إجابة الطالب: {student_answer}

أعط درجة من 10 وفسر السبب بصيغة JSON فقط:
{{"score": X, "feedback": "..."}}
"""

    try:
        completion = client.chat.completions.create(
            model="google/gemma-3n-e2b-it:free",
            messages=[{"role": "user", "content": prompt}]
        )

        full_text = completion.choices[0].message.content.strip()
        json_start = full_text.find('{')
        json_end = full_text.rfind('}') + 1

        if json_start == -1 or json_end == -1:
            return {"score": 0, "feedback": "⚠️ لم يتم العثور على JSON في الرد."}

        json_str = full_text[json_start:json_end]

        try:
            data = json.loads(json_str)
        except json.JSONDecodeError:
            return {"score": 0, "feedback": "⚠️ خطأ في تحليل JSON من رد الموديل."}

        score = data.get("score", 0)
        feedback = data.get("feedback", "⚠️ لا توجد تغذية راجعة.")

        try:
            requests.post(GOOGLE_SCRIPT_URL, json={
                "question": question,
                "model_answer": model_answer,
                "student_answer": student_answer,
                "score": score,
                "feedback": feedback
            })
        except Exception as e:
            print("⚠️ خطأ في إرسال البيانات إلى Google Sheets:", e)

        return {"score": score, "feedback": feedback}

    except Exception as e:
        return {"score": 0, "feedback": f"❌ خطأ في التقييم: {str(e)}"}

@app.post("/evaluate", response_model=GradeOutput)
def evaluate_api(data: GradeInput):
    if not data.question.strip() or not data.model_answer.strip() or not data.student_answer.strip():
        raise HTTPException(status_code=400, detail="جميع الحقول: question, model_answer, student_answer مطلوبة")
    return evaluate_logic(data.question, data.model_answer, data.student_answer)

def find_free_port(start=8000, end=8100):
    for port in range(start, end + 1):
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
            try:
                s.bind(("", port))
                return port
            except OSError:
                continue
    raise RuntimeError("لم يتم العثور على أي بورت مجاني في النطاق المحدد")

def close_existing_tunnels():
    api_url = "http://localhost:4040/api/tunnels"
    try:
        response = requests.get(api_url)
        tunnels = response.json().get("tunnels", [])
        for tunnel in tunnels:
            name = tunnel.get("name")
            public_url = tunnel.get("public_url")
            del_url = f"{api_url}/{name}"
            del_resp = requests.delete(del_url)
            if del_resp.status_code == 204:
                print(f"✅ تم إغلاق التانل: {public_url}")
            else:
                print(f"⚠️ فشل في إغلاق التانل: {public_url}")
    except Exception as e:
        print(f"⚠️ خطأ في إغلاق التانلز القديمة: {e}")

PORT = find_free_port()
print(f"🔍 تم العثور على بورت فاضي: {PORT}")

close_existing_tunnels()

public_url = ngrok.connect(PORT)
print(f"🔗 رابط الـ API العام عبر ngrok: {public_url}")

try:
    requests.post(GOOGLE_SCRIPT_URL, json={"url": str(public_url)})
    print("✅ تم إرسال رابط ngrok إلى Google Sheets")
except Exception as e:
    print("❌ فشل في إرسال رابط ngrok:", e)

def start_fastapi():
    print(f"🚀 تشغيل FastAPI على http://0.0.0.0:{PORT}")
    uvicorn.run(app, host="0.0.0.0", port=PORT)

# threading.Thread(target=start_fastapi, daemon=True).start()
if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=PORT)

