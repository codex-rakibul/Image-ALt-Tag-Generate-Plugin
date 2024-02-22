
const API_ENDPOINT = 'https://api.openai.com/v1/engines/davinci-codex/completions';
const API_KEY = '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';

function  generateAIAlternativeText(imageURL) {
    console.log(imageURL);

    const prompt = "Travel Title";
    const data = {
        prompt: prompt,
        max_tokens: 64,
        temperature: 0.5,
        n: 1,
        stop: null,
    };
    fetch(API_ENDPOINT, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${API_KEY}`,
        },
        body: JSON.stringify(data),
    }).then(response => response.json()).then(response => {
        const text = response.choices[0].text;
        console.log("Text", test);
    }).catch(error => {
        console.log(error);
    });
}