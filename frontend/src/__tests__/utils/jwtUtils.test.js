/* eslint-disable max-len */
import {getJWTRefreshTime, isJWTActual, parseJWT} from '../../utils/jwtUtils';

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

describe('getJWTRefreshTime:', () => {
    it('Should return calculated time for refresh', () => {
        expect(
            getJWTRefreshTime(
                'eyJ0eXAiOiJKV1QiLxCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJuZXd0b24tdGVjaG5vbG9neVwvaW52ZXN0b3JfdGVzdGluZyIsImF1ZCI6Im5ld3Rvbi10ZWNobm9sb2d5XC9pbnZlc3Rvcl90ZXN0aW5nXC9zZXJ2aWNlIiwiaWF0IjoxNjI4ODQ0NzczLCJleHAiOjE2Mjg4NDUzNzMsInN1YiI6InRlc3RAdGVzdC5jb20iLCJ1dWlkIjoiMWViZmMxM2QtNzNkNC02MGE4LWE3MzAtMDI0MmFjMTIwMDA3IiwiZmxvdyI6InNpZ251cCJ9.aPpfATbSFwCHeA3lpfYsvQr0haXG8gIQ87zqsZhVnWo-otEYW9D92aaa49zRY8YjjH5SoY9XHjNTqMoyMhpHbN1Brsb6IECtYTMCd4ysTQhPnU9r6ifcZdV9DJKCqc6WftR8ZZlXch-cV_d6dfd4tk10ngxZ6gkICr-idiH4LzW4E9Y1M3Na66j4MhQUXpWGN-s5UmdwDyo7lF6enfKr6542Ui4p2uWbpUAZLn9tGxCMstTS9pfoy2I90hYhnV7iaZsCu6B-OL_DMej8Xsh_XbtE4RPcbnj_cP35VXxihUmPb0gWkY35051jqXHGCdJ-_aPWCLqWFBwT4jVEp7N2Jg',
                1628844773000,
            ),
        ).toBe(480000);
        expect(
            getJWTRefreshTime(
                'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJuZXd0b24tdGVjaG5vbG9neVwvaW52ZXN0b3JfdGVzdGluZyIsImF1ZCI6Im5ld3Rvbi10ZWNobm9sb2d5XC9pbnZlc3Rvcl90ZXN0aW5nXC9zZXJ2aWNlIiwiaWF0IjoxNjI4ODQ2MDgxLCJleHAiOjE2Mjg4NDY2ODEsInN1YiI6InRlc3RAdGVzdC5jb20iLCJ1dWlkIjoiMWViZmMxNmUtMmFmNy02ZTgwLTk1ZjktMDI0MmFjMTIwMDA3IiwiZmxvdyI6InNpZ251cCJ9.SPn_SLoZljBJo_1oel0nMGnSziIdSpb78NtopEv4s5XV7yS6D-2k5eAirVFFhegsXxxc6LRYp94KB2_-LKJtb39J5aCfetapvrsvWzt1Bo8O3RAkKYIRgoUiWRGPbb5sRPPMJaEjx_laHgImx0keVOMhr0v9gp-19V2TQ2hVaUIcGY4H6ne4x3hGKJtDr458xBK88OFfQ6ZJu9r0-TBcPzqYJKwMU_i91LOJXmW8kEAVpUcnWuaDrhsggvtpW9E8GU84aXvght1iYhu_l_kM5-RNTpsBjUJHk0ntuDtmdtbKUtNCIBis5RQumn40livNZRrg2Mqs-cvW5M8ZUuwUZA',
                1628846121839,
            ),
        ).toBe(439161);
    });
});

describe('isJWTActual:', () => {
    it('Should return true for live token', () => {
        expect(
            isJWTActual(
                'eyJ0eXAiOiJKV1QiLxCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJuZXd0b24tdGVjaG5vbG9neVwvaW52ZXN0b3JfdGVzdGluZyIsImF1ZCI6Im5ld3Rvbi10ZWNobm9sb2d5XC9pbnZlc3Rvcl90ZXN0aW5nXC9zZXJ2aWNlIiwiaWF0IjoxNjI4ODQ0NzczLCJleHAiOjE2Mjg4NDUzNzMsInN1YiI6InRlc3RAdGVzdC5jb20iLCJ1dWlkIjoiMWViZmMxM2QtNzNkNC02MGE4LWE3MzAtMDI0MmFjMTIwMDA3IiwiZmxvdyI6InNpZ251cCJ9.aPpfATbSFwCHeA3lpfYsvQr0haXG8gIQ87zqsZhVnWo-otEYW9D92aaa49zRY8YjjH5SoY9XHjNTqMoyMhpHbN1Brsb6IECtYTMCd4ysTQhPnU9r6ifcZdV9DJKCqc6WftR8ZZlXch-cV_d6dfd4tk10ngxZ6gkICr-idiH4LzW4E9Y1M3Na66j4MhQUXpWGN-s5UmdwDyo7lF6enfKr6542Ui4p2uWbpUAZLn9tGxCMstTS9pfoy2I90hYhnV7iaZsCu6B-OL_DMej8Xsh_XbtE4RPcbnj_cP35VXxihUmPb0gWkY35051jqXHGCdJ-_aPWCLqWFBwT4jVEp7N2Jg',
                1628844773000,
            ),
        ).toBe(true);
    });

    it('Should return false for outdated token', () => {
        expect(
            isJWTActual(
                'eyJ0eXAiOiJKV1QiLxCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJuZXd0b24tdGVjaG5vbG9neVwvaW52ZXN0b3JfdGVzdGluZyIsImF1ZCI6Im5ld3Rvbi10ZWNobm9sb2d5XC9pbnZlc3Rvcl90ZXN0aW5nXC9zZXJ2aWNlIiwiaWF0IjoxNjI4ODQ0NzczLCJleHAiOjE2Mjg4NDUzNzMsInN1YiI6InRlc3RAdGVzdC5jb20iLCJ1dWlkIjoiMWViZmMxM2QtNzNkNC02MGE4LWE3MzAtMDI0MmFjMTIwMDA3IiwiZmxvdyI6InNpZ251cCJ9.aPpfATbSFwCHeA3lpfYsvQr0haXG8gIQ87zqsZhVnWo-otEYW9D92aaa49zRY8YjjH5SoY9XHjNTqMoyMhpHbN1Brsb6IECtYTMCd4ysTQhPnU9r6ifcZdV9DJKCqc6WftR8ZZlXch-cV_d6dfd4tk10ngxZ6gkICr-idiH4LzW4E9Y1M3Na66j4MhQUXpWGN-s5UmdwDyo7lF6enfKr6542Ui4p2uWbpUAZLn9tGxCMstTS9pfoy2I90hYhnV7iaZsCu6B-OL_DMej8Xsh_XbtE4RPcbnj_cP35VXxihUmPb0gWkY35051jqXHGCdJ-_aPWCLqWFBwT4jVEp7N2Jg',
                1628854325816,
            ),
        ).toBe(false);
    });
});
