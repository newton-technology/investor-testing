import axios from 'axios';

// axios.defaults.headers = {'Access-Control-Allow-Origin': 'http://localhost:9000'};

export const CategoryTestApi = {
    getTest(id: string) {
        // return axios.get('http://localhost:9000/api/investor_testing/test/id').then((response) => {
        //     console.log(response);
        // });

        return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve({
                    id: 3,
                    createdAt: 1627894786,
                    updatedAt: 1627894815,
                    category: {
                        id: 1,
                        code: 'CODE',
                        name: 'Производные финансовые инструменты',
                        description:
                            'Облигации, доход по которым зависит от ' +
                            'наступления или не наступления одного или нескольких случаев',
                    },
                    status: 'passed',
                    questions: [
                        {
                            id: 4,
                            question: 'Что такое облигации?',
                            answersCountToChooseMin: 1,
                            answersCountToChooseMax: 2,
                            answers: [
                                {
                                    id: 2,
                                    answer:
                                        'Эмиссионная долговая ценная бумага, владелец ' +
                                        'которой имеет право получить её номинальную стоимость ' +
                                        'деньгами или имуществом в установленный ею срок ' +
                                        'от того, кто её выпустил (эмитента)',
                                    selected: false,
                                },
                                {
                                    id: 3,
                                    answer:
                                        'Эмиссионная долговая ценная бумага, владелец ' +
                                        'которой имеет право получить её номинальную стоимость ',
                                    selected: false,
                                },
                            ],
                        },
                        {
                            id: 5,
                            question:
                                'Если Вы при инвестировании совершаете маржинальные/необеспеченные сделки, ' +
                                'как правило, размер возможных убытков:',
                            answersCountToChooseMin: 1,
                            answersCountToChooseMax: 1,
                            answers: [
                                {
                                    id: 7,
                                    answer: 'Больше, чем при торговле только на собственные средства',
                                    selected: false,
                                },
                                {
                                    id: 8,
                                    answer: 'Аналогичен размеру при торговле только на собственные средства',
                                    selected: false,
                                },
                            ],
                        },
                    ],
                });
            }, 2000);
        });
    },
};
