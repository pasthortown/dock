# -*- coding: utf-8 -*-
from tornado import httpserver
from tornado import gen
from tornado.ioloop import IOLoop
import tornado.web
from tornado.escape import json_encode, json_decode
from chatterbot import ChatBot
from chatterbot.trainers import ListTrainer
from adapters.date_time_adapter import DateTimeAdapter

class MainHandler(tornado.web.RequestHandler):
    def set_default_headers(self):
        self.set_header("Access-Control-Allow-Origin", "*")
        self.set_header("Access-Control-Allow-Headers", "x-requested-with")
        self.set_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
    def get(self):
        self.write('Saludos.')
    def post(self):
        chatbot = ChatBot(
            "Siturin-Chatbot",
            storage_adapter='chatterbot.storage.MongoDatabaseAdapter',
            database_uri='mongodb://admin:adminmongo@mongo:27017/',
            database='chatterbot',
            logic_adapters=[
                {
                    'import_path': 'chatterbot.logic.SpecificResponseAdapter',
                    'input_text': 'Hola, como estas?',
                    'output_text': 'Hola, muy bien. Y espero que tú también'
                },
                {
                    'import_path': 'chatterbot.logic.BestMatch',
                    "statement_comparison_function": "chatterbot.comparisons.levenshtein_distance",
                    "response_selection_method": "chatterbot.response_selection.get_most_frequent_response"
                },
                {
                    'import_path': 'adapters.date_time_adapter.DateTimeAdapter'
                },
                {
                    'import_path': 'chatterbot.logic.MathematicalEvaluation',
                },
                {
                    'import_path': 'chatterbot.logic.LowConfidenceAdapter',
                    'threshold': 0.60,
                    'default_response': 'Disculpa, no te he entendido bien. ¿Puedes decirmelo con mayor claridad?.'
                }
            ],
            trainer='chatterbot.trainers.ListTrainer'
        )
        chatbot.set_trainer(ListTrainer)
        recibido = self.request.body
        entendido = json_decode(recibido)['decir']
        try:
            respuesta = str(chatbot.get_response(entendido))
        except:
            respuesta = "Disculpa, no te he entendido bien. ¿Puedes decirmelo con mayor claridad?."
        self.write(json_encode({'data': respuesta}))
        
def main():
    app = tornado.web.Application([
        (r"/?", MainHandler),
    ])
    http_server = tornado.httpserver.HTTPServer(app)
    http_server.listen(5000)
    IOLoop.instance().start()

if __name__ == '__main__':
    main()