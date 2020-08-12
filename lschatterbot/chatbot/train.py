from chatterbot import ChatBot
from chatterbot.trainers import ChatterBotCorpusTrainer
import os

def setup():
    chatbot = ChatBot(
        "Siturin-Chatbot",
        storage_adapter='chatterbot.storage.MongoDatabaseAdapter',
        database_uri='mongodb://mongo:27017/',
        database='chatterbot',
        preprocessors=[
            'chatterbot.preprocessors.clean_whitespace'
        ],
    )
    
    chatbot.set_trainer(ChatterBotCorpusTrainer) 
    
    # for directory in os.listdir('./data/'):
    #     for file in os.listdir('./data/' + directory + '/'):
    #         chatbot.train('./data/' + directory + '/' + file)

    for file in os.listdir('./data/mias/'):
        chatbot.train('./data/mias/' + file)    
        
setup()