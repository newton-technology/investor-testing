import {emailEllipsisFormat} from '../../utils/emailEllipsisFormat';

describe('emailEllipsisFormat:', () => {
    it('Should return cutted email with ellipses', () => {
        expect(emailEllipsisFormat('longlocaldomain@domain.com')).toEqual('longloc...@domain.com');
    });

    it('Should return full email', () => {
        expect(emailEllipsisFormat('short@domain.com')).toEqual('short@domain.com');
    });
});
