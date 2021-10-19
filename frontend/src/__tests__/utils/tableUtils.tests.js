import {dateFormatter, unixTime} from '../../utils/tableUtils';

describe('Formatted dates', () => {
    it('Should return the correct time in the Moscow time zone', () => {
        expect(dateFormatter(1633430355, 'D MMMM YYYY; HH:mm (МСК)')).toEqual('5 октября 2021; 13:39 (МСК)');
    });

    it('Should returned the correct formatted End date in the Moscow time zone.', () => {
        expect(unixTime('2021-10-08', '23:59')).toEqual(1633726740);
    });

    it('Should returned the correct formatted start date in the Moscow time zone.', () => {
        expect(unixTime('2021-10-08', '00:00')).toEqual(1633640400);
    });
});
