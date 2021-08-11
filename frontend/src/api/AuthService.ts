import axios from 'axios';
class AuthService {
    private url = `${process.env.REACT_APP_API_URL}/authorization`;

    public init() {
        return this;
    }

    public login() {
        return this;
    }

    public logout() {
        return this;
    }

    public refresh() {
        return this;
    }
}

export default new AuthService();
axios.get('http://localhost:9000/api/investor_testing/categories').then((c) => console.log(c));
