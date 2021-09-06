export const emailEllipsisFormat = (email: string): string => {
    const [local, domain] = email.split('@');
    const fomatedLocal = local.length > 7 ? `${local.substr(0, 7)}...` : local;

    return `${fomatedLocal}@${domain}`;
};
