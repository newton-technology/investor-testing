import axios from 'axios';

export const ManagmentApi = {
    getAllTests() {
        return axios.get(`${process.env.REACT_APP_API_URL}/management/tests`).then((response) => {
            console.log(response);
        });
    },
};
