function cleanJsonResponse(text) {
  return text
    .replace(/```json\s*/g, '')
    .replace(/```\s*$/g, '')
    .replace(/^\s*`/g, '')
    .replace(/`\s*$/g, '')
    .trim();
}

async function generateQCM() {
  const apiKey = "AIzaSyBQlEUG_Tpan-EO_PlxXaT_4kWm0ZfVK0U".trim();
  const promptInput = document.getElementById("promptInput");
  const loading = document.getElementById("loading");
  const error = document.getElementById("error");
  const container = document.getElementById("qcm-container");

  if (!apiKey) {
    error.textContent = "Veuillez entrer votre clé API Gemini.";
    return;
  }

  if (!promptInput.value.trim()) {
    error.textContent = "Veuillez entrer un sujet ou un texte.";
    return;
  }

  try {
    loading.style.display = "block";
    error.textContent = "";
    container.innerHTML = "";

    const prompt = `Tu es un expert dans le domaine spécifique mentionné. Génère 10 questions QCM complexes et détaillées sur ce sujet, en te concentrant sur les aspects techniques, légaux, procéduraux et spécifiques du domaine. Les questions doivent tester une compréhension approfondie du sujet.

Pour chaque question :
- Formule des questions complexes qui nécessitent une réflexion approfondie
- Inclus des détails spécifiques au domaine
- Mentionne des lois, procédures, ou concepts techniques pertinents
- Les réponses doivent être précises et spécifiques au domaine

Format JSON :
[
  {
    "question": "Question complexe et détaillée",
    "options": ["Réponse précise 1", "Réponse précise 2", "Réponse précise 3"],
    "answer": "La réponse correcte et détaillée",
    "explanation": "Explication détaillée de la réponse correcte"
  }
]

Sujet : ${promptInput.value}`;

    const requestBody = {
      contents: [{
        parts: [{
          text: prompt
        }]
      }]
    };

    const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${apiKey}`, {
      method: "POST",
      headers: { 
        "Content-Type": "application/json"
      },
      body: JSON.stringify(requestBody)
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(`Erreur HTTP: ${response.status} - ${errorData.error?.message || 'Erreur inconnue'}`);
    }

    const data = await response.json();
    const rawText = data.candidates?.[0]?.content?.parts?.[0]?.text;
    
    if (!rawText) {
      throw new Error("Pas de réponse de l'API");
    }

    const cleanedText = cleanJsonResponse(rawText);
    console.log("Réponse nettoyée:", cleanedText);
    const questions = JSON.parse(cleanedText);
    afficherQCM(questions);
  } catch (err) {
    error.textContent = `Erreur: ${err.message}`;
    console.error("Erreur détaillée:", err);
  } finally {
    loading.style.display = "none";
  }
}

function afficherQCM(questions) {
  const container = document.getElementById("qcm-container");
  container.innerHTML = "";

  questions.forEach((q, index) => {
    const questionDiv = document.createElement("div");
    questionDiv.className = "question-container";
    
    const questionText = document.createElement("p");
    questionText.textContent = `${index + 1}. ${q.question}`;
    questionDiv.appendChild(questionText);

    const optionsDiv = document.createElement("div");
    q.options.forEach(option => {
      const btn = document.createElement("button");
      btn.textContent = option;
      btn.onclick = () => {
        const feedback = questionDiv.querySelector(".feedback");
        const explanation = questionDiv.querySelector(".explanation");
        
        if (option === q.answer) {
          feedback.textContent = "✓ Correct !";
          feedback.className = "feedback correct";
        } else {
          feedback.textContent = "✗ Incorrect !";
          feedback.className = "feedback incorrect";
        }
        
        explanation.textContent = q.explanation;
        explanation.style.display = "block";
        optionsDiv.querySelectorAll("button").forEach(b => b.disabled = true);
      };
      optionsDiv.appendChild(btn);
    });
    questionDiv.appendChild(optionsDiv);

    const feedback = document.createElement("p");
    feedback.className = "feedback";
    questionDiv.appendChild(feedback);

    const explanation = document.createElement("p");
    explanation.className = "explanation";
    explanation.style.display = "none";
    questionDiv.appendChild(explanation);

    container.appendChild(questionDiv);
  });
} 