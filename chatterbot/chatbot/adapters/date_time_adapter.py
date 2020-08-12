from chatterbot.logic import LogicAdapter
from chatterbot.conversation import Statement
import datetime
from nltk import NaiveBayesClassifier

class DateTimeAdapter(LogicAdapter):
    def __init__(self, **kwargs):
        super(DateTimeAdapter, self).__init__(**kwargs)

        self.positive = kwargs.get('positive', [
            'Qué hora es?',
            'tienes la hora?',
            'puedes decirme la hora?',
            'sabes qué hora es?',
            'Qué fecha es?',
            'tienes la fecha?',
            'puedes decirme la hora?',
            'sabes qué fecha es?',
            'conoces la fecha?',
            'conoces qué fecha es?',
            'conoces qué hora es?',
            'Qué fecha es hoy?',
            'Qué fecha llevamos?',
            'Qué fecha estamos?',
            'Qué día es hoy?',
            'Qué día es?',
            'Cuánto llevamos hoy?',
            'Dime la hora?',
            'Dime la fecha?',
            'Dime el día?',
            'Dame la fecha?',
            'Dame la hora?',
            'Sabes qué día es hoy?',
            'Sabes qué fecha es hoy?',
            'Sabes la fecha de hoy?',
            'Conoces la fecha de hoy?',
            'Dime la fecha de hoy?',
            'Qué hora es',
            'tienes la hora',
            'puedes decirme la hora',
            'sabes qué hora es',
            'Qué fecha es',
            'tienes la fecha',
            'puedes decirme la hora',
            'sabes qué fecha es',
            'conoces la fecha',
            'conoces qué fecha es',
            'conoces qué hora es',
            'Qué fecha es hoy',
            'Qué fecha llevamos',
            'Qué fecha estamos',
            'Qué día es hoy',
            'Qué día es',
            'Cuánto llevamos hoy',
            'Dime la hora',
            'Dime la fecha',
            'Dime el día',
            'Dame la fecha',
            'Dame la hora',
            'Sabes qué día es hoy',
            'Sabes qué fecha es hoy',
            'Sabes la fecha de hoy',
            'Conoces la fecha de hoy',
            'Dime la fecha de hoy'
        ])

        self.negative = kwargs.get('negative', [
            'ia',
            'adiós',
            'es hora de bailar',
            'Qué haces?',
            'Como estas?',
            'que haces?',
            'Si',
            'No',
            'si',
            'no',
            'hoy es un gran día',
            'hoy va a ser una fecha especial',
            'tienes tiempo?',
            'hoy qué haras?',
            'hola',
            'hola que',
            'besame',
            'amame'
        ])

        labeled_data = (
            [(name, 0) for name in self.negative] +
            [(name, 1) for name in self.positive]
        )

        train_set = [
            (self.time_question_features(text), n) for (text, n) in labeled_data
        ]

        self.classifier = NaiveBayesClassifier.train(train_set)

    def time_question_features(self, text):
        features = {}
        # A list of all words from the known sentences
        all_words = " ".join(self.positive + self.negative).split()
        filter(lambda a: a !=  'es', all_words)
        filter(lambda a: a !=  'el', all_words)
        filter(lambda a: a !=  'la', all_words)
        # A list of the first word in each of the known sentence
        all_first_words = []
        for sentence in self.positive + self.negative:
            all_first_words.append(
                sentence.split(' ', 1)[0]
            )
        for word in text.split():
            features['first_word({})'.format(word)] = (word in all_first_words)

        for word in text.split():
            features['contains({})'.format(word)] = (word in all_words)

        for letter in 'abcdefghijklmnopqrstuvwxyz':
            features['count({})'.format(letter)] = text.lower().count(letter)
            features['has({})'.format(letter)] = (letter in text.lower())

        return features

    def process(self, statement):
        time_features = self.time_question_features(statement.text.lower())
        confidence = self.classifier.classify(time_features)
        now = datetime.datetime.now()
        selected_statement = Statement(str(now.strftime('Hoy es el día %d del mes %m del año %Y y son las %I:%M %p')))
        selected_statement.confidence = confidence
        return selected_statement