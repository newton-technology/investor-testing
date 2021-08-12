/* eslint-disable max-len */
import {parseJwtRefreshTime} from '../../utils/parseJwtLifeTime';

describe('parseJwtRefreshTime:', () => {
    it('Should return 15% of time from real token', () => {
        expect(
            parseJwtRefreshTime(
                'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJuZXd0b24tdGVjaG5vbG9neVwvaW52ZXN0b3JfdGVzdGluZyIsImF1ZCI6Im5ld3Rvbi10ZWNobm9sb2d5XC9pbnZlc3Rvcl90ZXN0aW5nXC9zZXJ2aWNlIiwiaWF0IjoxNjI4Nzg1ODkzLCJleHAiOjE2Mjg3ODY0OTMsInN1YiI6InZhczEyMzFla0BtYWlsLnJ1IiwidXVpZCI6IjFlYmZiOGFiLWZmOTQtNjJjYy04MTVjLTAyNDJhYzEyMDAwNyIsImZsb3ciOiJzaWdudXAifQ.qnAdbUAlmL1fqYaBbdGy__DL93g9HwN4qQFUpiAabOGIJ_H_lJNPWObs_4HpMUxieDqJdGo80vU2gaaJJ4RY82YRjtw5d5R05S6_cbbb8CpRFiQ_-ZExNZpLwkaxVdFvV0OfV0-FLWBXkUPltqcjK34lyIrQ64_cQ-mz1LKqwqymSUFKC6Py1z0AJSylMdNiutUCY9KsZA09nFpbUjpDQkbb4-K87VI5x6v298faMLJ6VUXKfJ6dMyymnG0TDcostrFQx0-f1fKknmV1uDxYPKqVNnF5hW1Ks5DPWrzWG3wpSVXvxcdz2BU3pDu5UtdZsOJJ0a_kdGYjQ8XZHgS-Cg',
            ),
        ).toBe(510);
    });
});
