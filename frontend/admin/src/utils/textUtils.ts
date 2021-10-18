export const removeHint = (text: string): string => {
    text = text.replace(/<hint>.*<\/hint>/g, '');
    return text;
};
