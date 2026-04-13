const YOUTUBE_DOMAINS = ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be', 'www.youtu.be'];

const safeParseUrl = (value) => {
    try {
        return new URL(value);
    } catch {
        return null;
    }
};

const extractYouTubeId = (urlString) => {
    const parsed = safeParseUrl(urlString);
    if (!parsed || !YOUTUBE_DOMAINS.includes(parsed.hostname)) {
        return null;
    }

    if (parsed.hostname.includes('youtu.be')) {
        const shortId = parsed.pathname.split('/').filter(Boolean)[0];
        return shortId || null;
    }

    const watchId = parsed.searchParams.get('v');
    if (watchId) {
        return watchId;
    }

    const parts = parsed.pathname.split('/').filter(Boolean);
    const embedIndex = parts.indexOf('embed');
    if (embedIndex !== -1 && parts[embedIndex + 1]) {
        return parts[embedIndex + 1];
    }

    const shortsIndex = parts.indexOf('shorts');
    if (shortsIndex !== -1 && parts[shortsIndex + 1]) {
        return parts[shortsIndex + 1];
    }

    return null;
};

const appendParams = (baseUrl, params) => {
    const parsed = safeParseUrl(baseUrl);
    if (!parsed) {
        return baseUrl;
    }

    Object.entries(params).forEach(([key, value]) => {
        parsed.searchParams.set(key, value);
    });

    return parsed.toString();
};

export const getTrailerSource = (urlString, options = {}) => {
    const value = (urlString || '').trim();

    if (!value) {
        return { type: 'none', src: null };
    }

    const youtubeId = extractYouTubeId(value);
    if (youtubeId) {
        const baseEmbed = `https://www.youtube.com/embed/${youtubeId}`;
        const src = options.preview
            ? appendParams(baseEmbed, {
                autoplay: '1',
                mute: '0',
                controls: '0',
                rel: '0',
                playsinline: '1',
                loop: '1',
                playlist: youtubeId,
            })
            : appendParams(baseEmbed, {
                rel: '0',
            });

        return { type: 'embed', src };
    }

    return { type: 'none', src: null };
};
