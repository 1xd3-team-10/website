// change in links between prod and dev

const wantedUrl = "https://cs1xd3.cas.mcmaster.ca/";
const currentURL = window.location.href;

/**
 *
 * @param {string} prefix prefix to add to all urls
 */
const changeTags = (prefix) => {
    const anchorTags = document.querySelectorAll("a");
    const linkTags = document.querySelectorAll("link");
    anchorTags.forEach((tag) => {
        console.log(tag.href.split(RegExp("https?:\/\/[^\/]+")));
        if (!tag.href.startsWith(prefix)) {
            tag.href =
                "/" + prefix + tag.href.split(RegExp("https?:\/\/[^\/]+"))[1];
        }
        console.log(tag);
    });

    linkTags.forEach((tag) => {
        console.log(tag.href.split(RegExp("https?:\/\/[^\/]+")));
        if (!tag.href.startsWith(prefix)) {
            tag.href =
                "/" + prefix + tag.href.split(RegExp("https?:\/\/[^\/]+"))[1];
        }
        console.log(tag);
    });
};

if (currentURL.startsWith(wantedUrl)) {
    const user = currentURL.split(wantedUrl)[1].split("/")[0];
    changeTags(user);
}
