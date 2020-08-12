from chatterbot import ChatBot
from chatterbot.trainers import ChatterBotCorpusTrainer
import os

def setup():
    chatbot = ChatBot(
        "Siturin-Chatbot",
        storage_adapter='chatterbot.storage.MongoDatabaseAdapter',
        database_uri='mongodb://admin:adminmongo@mongo:27017/',
        database='chatterbot',
        preprocessors=[
            'chatterbot.preprocessors.clean_whitespace'
        ],
    )
    for file in os.listdir('./data/'):
        chatbot.set_trainer(ChatterBotCorpusTrainer) 
        chatbot.train('./data/' + file)

setup()