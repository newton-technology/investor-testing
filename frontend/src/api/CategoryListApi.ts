import axios from 'axios';

export const CategoryListApi = {
    getUser() {
        return axios.get('http://localhost:3000/api/investor_testing/tests').then((response) => {
            console.log(response);
        });
    },
    getTests() {
        // axios.defaults.headers.get = {'Access-Control-Allow-Origin': '*', 'Content-Type': 'application/json'};

        // return axios.get('http://localhost:3000/api/investor_testing/tests').then((response) => {
        //     console.log(response);
        // });
        return new Promise((resolve, reject) => {
            resolve([
                {
                    id: 3,
                    createdAt: 1627894786,
                    updatedAt: 1627894815,
                    category: {
                        id: 1,
                        code: 'CODE',
                        name: 'Производные финансовые инструменты',
                        description: 'Необеспеченные сделки',
                    },
                    status: 'passed',
                },
                {
                    id: 4,
                    createdAt: 1627894786,
                    updatedAt: 1627894815,
                    category: {
                        id: 1,
                        code: 'CODE',
                        name: 'Производные финансовые инструменты',
                        description:
                            'Договоры, являющиеся финансовым инструментом' +
                            ' и не предназначенные для квалифицированных инвесторов',
                    },
                    status: 'not_passed',
                },
                {
                    id: 5,
                    createdAt: 1627894786,
                    updatedAt: 1627894815,
                    category: {
                        id: 1,
                        code: 'CODE',
                        name: 'Производные финансовые инструменты',
                        description:
                            'Сделки по приобретению инвестиционных паев закрытых ' +
                            'паевых инвестиционных фондов, не предназначенных для' +
                            'квалифицированных инвесторов, требующих проведения тестирования.',
                    },
                    status: 'not_passed',
                },
            ]);
        });
    },
};
