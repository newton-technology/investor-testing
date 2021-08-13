import axios from 'axios';

export const CategoryListApi = {
    getUser() {
        return axios.get('http://localhost:3000/api/investor_testing/tests').then((response) => {
            console.log(response);
        });
    },
    getTests() {
        console.log(axios.defaults.headers);
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
                        description:
                            'Облигации, доход по которым зависит от наступления или ' +
                            'не наступления одного или нескольких случаев',
                    },
                    status: 'passed',
                },
            ]);
        });
    },
};
