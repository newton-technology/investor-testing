import {removeHint} from '../../utils/textUtils';

describe('Remove text inside hint tag', () => {
    it('Should return text without a hint tag when it is in the middle of the text', () => {
        expect(removeHint('Lorem ipsum dolor sit amet, consectetur<hint>some text</hint> adipisicing elit.')).toBe(
            'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        );
    });

    it('Should return text without hint tag when it is at the beginning of the text', () => {
        expect(removeHint('<hint>some text</hint>Lorem ipsum dolor sit amet, consectetur adipisicing elit.')).toBe(
            'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        );
    });

    it('Should return text without hint tag when it is at the end of the text', () => {
        expect(removeHint('Lorem ipsum dolor sit amet, consectetur adipisicing elit.<hint>some text</hint>')).toBe(
            'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        );
    });
});
