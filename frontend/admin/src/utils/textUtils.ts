export const removeHint = (text: string) => {
    text = text.replace(/<hint>.*<\/hint>/g, '');
    return text;
};
