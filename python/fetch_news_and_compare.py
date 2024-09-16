import requests
from bs4 import BeautifulSoup
from transformers import BertTokenizer, BertModel
import torch
import sys
import io
import json
from googletrans import Translator

# Set UTF-8 as default encoding for stdout and stderr
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')

# Initialize the mBERT model and tokenizer
tokenizer = BertTokenizer.from_pretrained('bert-base-multilingual-cased')
model = BertModel.from_pretrained('bert-base-multilingual-cased')

# Initialize the translator
translator = Translator()

def fetch_news():
    url = "https://time.mk/?new=true&topic=makedonija"
    response = requests.get(url)

    if response.status_code == 200:
        soup = BeautifulSoup(response.content, 'lxml')
        articles = soup.find_all('div', class_='cluster')  # Adjust class based on the HTML structure
        news = []

        for article in articles:
            content = article.find(class_='snippet').text.strip()
            source = article.find(class_='source')
            title_h1 = article.find('h1')
            title = title_h1.find('a').text
            link = article.find('h1').find('a')['href']
            news.append({
                'title': title,
                'url': link,
                'content': content,
                'source': source
            })
        return news
    else:
        return []

# Function to encode a sentence using mBERT
def encode_sentence(sentence):
    inputs = tokenizer(sentence, return_tensors='pt', truncation=True, padding=True, max_length=512)
    with torch.no_grad():
        outputs = model(**inputs)
    return outputs.last_hidden_state.mean(dim=1)

# Function to compute cosine similarity between two sentence embeddings
def cosine_similarity(vec1, vec2):
    cos_sim = torch.nn.functional.cosine_similarity(vec1, vec2)
    return cos_sim.item() * 100  # Convert to percentage

# Function to compare news content using mBERT embeddings
def compare_news(news1, news2):
    vec1 = encode_sentence(news1)
    vec2 = encode_sentence(news2)
    similarity = cosine_similarity(vec1, vec2)
    return similarity

# Function to translate text to English
def translate_to_english(text):
    translation = translator.translate(text, src='mk', dest='en')
    return translation.text

if __name__ == "__main__":
    news = fetch_news()

    if len(news) > 1:
        similarity_results = []
        for i in range(len(news) - 1):
            for j in range(i + 1, len(news)):
                # Compare original content
                original_similarity = compare_news(news[i]['content'], news[j]['content'])

                # Translate to English and compare
                translated_content1 = translate_to_english(news[i]['content'])
                translated_content2 = translate_to_english(news[j]['content'])
                translated_similarity = compare_news(translated_content1, translated_content2)

                similarity_results.append({
                    'news1_title': news[i]['title'],
                    'news1_content': news[i]['content'],
                    'news1_url': news[i]['url'],
                    'news2_title': news[j]['title'],
                    'news2_content': news[j]['content'],
                    'news2_url': news[j]['url'],
                    'similarity_original': f"{original_similarity:.2f}%",
                    'similarity_translated': f"{translated_similarity:.2f}%"
                })

        # Output the results as JSON
        output = {
            'fetched_news': news,
            'comparison_results': similarity_results
        }

        # Output the results as JSON
        json_data = json.dumps(output, ensure_ascii=False, indent=2)
        print(json_data)
    else:
        print("Not enough news to compare.")
