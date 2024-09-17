from transformers import BertTokenizer, BertModel
import torch
import json
import io
import sys
from googletrans import Translator

# Initialize mBERT model and tokenizer
tokenizer = BertTokenizer.from_pretrained('bert-base-multilingual-cased')
model = BertModel.from_pretrained('bert-base-multilingual-cased')
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')
translator = Translator()
sys.stdout.flush()

def encode_sentence(sentence):
    inputs = tokenizer(sentence, return_tensors='pt', truncation=True, padding=True, max_length=512)
    with torch.no_grad():
        outputs = model(**inputs)
    return outputs.last_hidden_state.mean(dim=1)

def cosine_similarity(vec1, vec2):
    cos_sim = torch.nn.functional.cosine_similarity(vec1, vec2)
    return cos_sim.item() * 100  # Convert to percentage

def compare_news(news1, news2):
    vec1 = encode_sentence(news1)
    vec2 = encode_sentence(news2)
    return cosine_similarity(vec1, vec2)

def translate_to_english(text):
    translation = translator.translate(text, src='mk', dest='en')
    return translation.text

def compare_news_articles(news):
    results = []
    for i in range(len(news) - 1):
        for j in range(i + 1, len(news)):
            original_similarity = compare_news(news[i]['content'], news[j]['content'])
            translated_content1 = translate_to_english(news[i]['content'])
            translated_content2 = translate_to_english(news[j]['content'])
            translated_similarity = compare_news(translated_content1, translated_content2)

            results.append({
                'news1_title': news[i]['title'],
                'news1_content': news[i]['content'],
                'news1_url': news[i]['url'],
                'news2_title': news[j]['title'],
                'news2_content': news[j]['content'],
                'news2_url': news[j]['url'],
                'similarity_original': f"{original_similarity:.2f}",
                'similarity_translated': f"{translated_similarity:.2f}"
            })
    return results

def load_input_data(file_path):
    try:
        with open(file_path, 'r', encoding='utf-8') as file:
            input_data = json.load(file)
            return input_data
    except FileNotFoundError:
        print(f"Error: File {file_path} not found.")
        sys.exit(1)
    except json.JSONDecodeError:
        print("Error: Failed to decode JSON input.")
        sys.exit(1)

if __name__ == "__main__":
    #if len(sys.argv) < 2:
    #    print("Error: No input file path provided.")
    #    sys.exit(1)

    file_path = sys.argv[1]
    news_articles = load_input_data(file_path)
    results = compare_news_articles(news_articles)
    print(json.dumps(results, ensure_ascii=False, indent=2))

