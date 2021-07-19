function sendEventNotification(target, params) {

    const event = new CustomEvent("alpdesk", {
        detail: {
            type: 'route',
            target: target,
            params: params
        }
    });

    document.dispatchEvent(event);
}

function elementById(element) {

    if (document.getElementById(element) === null || document.getElementById(element) === undefined) {
        return null;
    }

    return document.getElementById(element);

}

function elementClick(element, callback) {

    const e = elementById(element);
    if (e !== null) {

        e.onclick = callback;

    }

}


