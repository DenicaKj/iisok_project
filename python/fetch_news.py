import requests
from bs4 import BeautifulSoup
import json
import io
import sys

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')

def fetch_news():
    url = "https://time.mk/?new=true&topic=makedonija"
    response = requests.get(url)
    if response.status_code == 200:
        soup = BeautifulSoup(response.content, 'lxml')
        articles = soup.find_all('div', class_='cluster')
        news = []

        for article in articles:
            content = article.find(class_='snippet').text.strip()
            source = article.find(class_='source').text.strip() if article.find(class_='source') else 'Unknown'
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

if __name__ == "__main__":
    news = fetch_news()
    print(json.dumps(news, ensure_ascii=False, indent=2))
