import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["output", "loader"];

  connect() {
    // this.read2();
    this.readStream();
  }


  async read2() {

      const eventSource = new EventSource('/stream-json');

      eventSource.onmessage = (event) => {
          console.log(event.data);
          const data = JSON.parse(event.data);
          console.log(data);
          const div = document.createElement("div");
          div.textContent = data.title;
          this.outputTarget.appendChild(div);
          this.outputTarget.scrollTop = this.outputTarget.scrollHeight;
      };

  }
  async readStream() {
    const response = await fetch('/stream-json');
    const reader = response.body.getReader();
    const decoder = new TextDecoder('utf-8');
    let buffer = '';

    while (true) {
      const { value, done } = await reader.read();
      if (done) {
        this.hideLoader();
        break;
      }

      buffer += decoder.decode(value, { stream: true });
      const lines = buffer.split('\n');
      buffer = lines.pop();

      for (const line of lines) {
        if (!line.trim()) continue;
        try {
          const json = JSON.parse(line);
          this.appendToken(json.token);
        } catch (e) {
          console.error("Invalid JSON:", line);
        }
      }
    }
  }

  appendToken(token) {
    const span = document.createElement('span');
    span.textContent = token + ' ';
    this.outputTarget.appendChild(span);
    this.outputTarget.scrollTop = this.outputTarget.scrollHeight;
  }

  hideLoader() {
    if (this.hasLoaderTarget) {
      this.loaderTarget.style.display = 'none';
    }
  }
}
