import openai
from flask import Flask, request
import requests

app = Flask(__name__)

# Replace with your actual OpenAI and Telegram keys
TELEGRAM_TOKEN = "7746509381:AAEtrmCbWqyBMyoopzf33SUa7OaAzqgo-68"
openai.api_key = "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"

def get_chatgpt_reply(message):
    response = openai.ChatCompletion.create(
        model="gpt-3.5-turbo",
        messages=[{"role": "user", "content": message}]
    )
    return response['choices'][0]['message']['content'].strip()

def send_telegram_message(chat_id, text):
    url = f"https://api.telegram.org/bot{TELEGRAM_TOKEN}/sendMessage"
    data = {"chat_id": chat_id, "text": text}
    requests.post(url, data=data)

@app.route("/gpt-3.5-turbo", methods=["POST"])
def webhook():
    data = request.get_json()
    if "message" in data and "text" in data["message"]:
        chat_id = data["message"]["chat"]["id"]
        text = data["message"]["text"]
        reply = get_chatgpt_reply(text)
        send_telegram_message(chat_id, reply)
    return "ok"

@app.route("/", methods=["GET"])
def home():
    return "EDITH Python AI bot is running!"
