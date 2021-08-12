/* eslint-disable max-len */
import {parseJWT} from '../../utils/parseJWT';

describe('parseJWT:', () => {
    it('Should return parsed obj', () => {
        const expected = {
            iss: 'newton-technology/investor_testing',
            aud: 'newton-technology/investor_testing/service',
            iat: 1628802016,
            exp: 1628802616,
            sub: 'test@test.com',
            uuid: '1ebfbb04-9ee9-657a-b2ef-0242ac120007',
            flow: 'signup',
        };

        expect(
            parseJWT(
                'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJuZXd0b24tdGVjaG5vbG9neVwvaW52ZXN0b3JfdGVzdGluZyIsImF1ZCI6Im5ld3Rvbi10ZWNobm9sb2d5XC9pbnZlc3Rvcl90ZXN0aW5nXC9zZXJ2aWNlIiwiaWF0IjoxNjI4ODAyMDE2LCJleHAiOjE2Mjg4MDI2MTYsInN1YiI6InRlc3RAdGVzdC5jb20iLCJ1dWlkIjoiMWViZmJiMDQtOWVlOS02NTdhLWIyZWYtMDI0MmFjMTIwMDA3IiwiZmxvdyI6InNpZ251cCJ9.Bv2Ch9BaZF6wwyTs-um-EYcYoEp0ZZ85iv0u4zguEMgn0rLhz7YLgH0kgeUqV36Gtohk4LAUFQN9JgXkvCzgV9h_6Qhy-5_MbcgHiFECsY7Hgp7DYdbDKQevuekaeHiKNl8-94P7s1u7MT0X3vj-xWEWkdFwtXNENTqQv3etSaJ31d9YvNyGsMwFcj6owPmrDzR6k0_M4LVjMxiOUvc3yJNXRYOH1deltwsjMHIOupCHungD8DQgR3DSVkOhWyIr-P4bEcJdnSHjns7GG2FM1g7Tp_a1dVsQN_RjEmaejFO_2ZGB_an00cA9GmuTuSKtUSwUQW0aL_Kh3nRjdIuI3Q',
            ),
        ).toMatchObject(expected);
    });
});
