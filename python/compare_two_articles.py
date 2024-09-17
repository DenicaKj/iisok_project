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

def compare_two_articles(article1, article2):
    # Original comparison
    original_similarity = compare_news(article1, article2)

    # Translate both articles to English
    translated_content1 = translate_to_english(article1)
    translated_content2 = translate_to_english(article2)

    # Compare the translated articles
    translated_similarity = compare_news(translated_content1, translated_content2)

    # Return the comparison result
    result = {'similarity_original': f"{original_similarity:.2f}"}
    return result

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Error: No input file path provided.")
        sys.exit(1)

    # Load the articles from the input file
    file_path = sys.argv[1]
    with open(file_path, 'r', encoding='utf-8') as file:
        data = json.load(file)
        article1 = data['article1']
        article2 = data['article2']

    # Compare the two articles
    comparison_result = compare_two_articles(article1, article2)

    # Print the result
    print(json.dumps(comparison_result, ensure_ascii=False, indent=2))
