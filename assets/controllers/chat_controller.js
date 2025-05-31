
import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["form", "input", "messages"];
  static values = {agent: String};

  async send(event) {
    event.preventDefault()
    const message = this.inputTarget.value
    if (!message.trim()) return

    this.addMessage("You", message)

    this.inputTarget.value = "--thinking--";
    const response = await fetch("/chat/api/chat/" + this.agentValue, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message }),
    })

    const data = await response.json()
    this.inputTarget.value = "";
    this.addMessage("Chad-<personality>", data.response)
  }

  addMessage(sender, text) {
    const div = document.createElement("div")
    div.innerHTML = `<strong>${sender}:</strong> ${text}`
    this.messagesTarget.appendChild(div)
    this.messagesTarget.scrollTop = this.messagesTarget.scrollHeight
  }
}
