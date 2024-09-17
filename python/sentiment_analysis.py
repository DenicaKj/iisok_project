import sys
import json
from transformers import pipeline
import io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')

# Initialize the sentiment analysis pipeline with a specific model
model_name = "nlptown/bert-base-multilingual-uncased-sentiment"
sentiment_analyzer = pipeline("sentiment-analysis", model=model_name)

def analyze_sentiment(text):
    result = sentiment_analyzer(text)
    return result[0]  # Assuming the result is a list of dictionaries

if __name__ == "__main__":
    # Get the article text from the command-line arguments
    article_text = sys.argv[1]

    # Analyze sentiment
    sentiment = analyze_sentiment(article_text)

    # Print the result as JSON
    print(json.dumps(sentiment))
