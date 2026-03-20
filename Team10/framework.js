const debug = true;

const componentCache = new Map();
const deferredFunctions = [];
/** @type {Array<[string, () => void, Array<() => Element | Element>]>} */
const updateEvents = [];

/**
 * @returns base url of the application
 */
const getBaseURL = () => {
    if (debug) return "http://127.0.0.1:8080/Team10/";
    return "https://cs1xd3.cas.mcmaster.ca/~sita1/Team10/";
};

const baseURL = getBaseURL();
const componentsDir = "components";

/**
 * Entry point
 * @param {string} rootId id of the root element
 */
const ApplyComponentFill = async (rootId) => {
    const root = document.querySelector(`#${rootId}`);
    if (!root) return;

    await ApplyComponentFillToNode(root);
};

/**
 * Runs component replacement on a subtree
 * @param {Element|DocumentFragment} rootNode
 */
const ApplyComponentFillToNode = async (rootNode) => {
    const components = FindComponentCallers(rootNode);
    await ReplaceComponents(components);
};

/**
 * Recursively finds elements with `comp` attribute
 * @param {Node} current
 * @returns {Element[]}
 */
const FindComponentCallers = (current) => {
    let components = [];

    if (!(current instanceof Element || current instanceof DocumentFragment)) {
        return components;
    }

    if (current instanceof Element && current.hasAttribute("comp")) {
        components.push(current);
    }

    for (const child of current.children ?? []) {
        components.push(...FindComponentCallers(child));
    }

    return components;
};

/**
 * Fetches and replaces components
 * @param {Element[]} components
 */
const ReplaceComponents = async (components) => {
    const parser = new DOMParser();

    for (const element of components) {
        const compName = element.getAttribute("comp");
        if (!compName) continue;
        let html = "";
        if (componentCache.has(compName)) {
            html = componentCache.get(compName);
        } else {
            const componentPath = `${baseURL}${componentsDir}/${compName}.html`;

            let response;
            try {
                response = await fetch(componentPath);
            } catch {
                errorMessage(`Failed to fetch ${componentPath}`);
                continue;
            }

            if (!response.ok) {
                errorMessage(`Error retrieving ${compName}.html`);
                continue;
            }

            html = await response.text();
            componentCache.set(compName, html);
        }

        html = ParseParameters(element, html);

        const doc = parser.parseFromString(html, "text/html");

        const fragment = document.createDocumentFragment();
        fragment.append(...doc.body.childNodes);

        fragment.querySelectorAll("script").forEach((script) => {
            const newScript = document.createElement("script");
            if (script.src) {
                newScript.src = script.src;
            } else {
                newScript.textContent = script.textContent;
            }
            document.head.appendChild(newScript);
        });
        await ApplyComponentFillToNode(fragment);
        element.replaceWith(fragment);
    }
};

/**
 * Extract parameters from component call
 * @param {Element} element
 * @param {string} componentHTML
 */
const ParseParameters = (element, componentHTML) => {
    const params = {};

    // slot support
    if (element.innerHTML.trim()) {
        params.children = element.innerHTML;
    }

    for (const attr of element.getAttributeNames()) {
        if (attr.startsWith("[") && attr.endsWith("]")) {
            const key = attr.slice(1, -1);
            params[key] = element.getAttribute(attr);
        }
    }

    return ApplyParams(componentHTML, params);
};

/**
 * Replaces {{ param }} placeholders
 * @param {string} html
 * @param {Object} params
 */
const ApplyParams = (html, params) => {
    return html.replace(/\{\{\s*(\w+)\s*\}\}/g, (_, key) =>
        key in params ? params[key] : "",
    );
};

/**
 * Debug helper
 */
const errorMessage = (message) => {
    if (debug) alert(message);
    console.error(message);
};

// EXPOSED FUNCTIONS

/**
 *
 * @param {Function} func function to defer run until after load
 */
const DeferRun = (func) => {
    deferredFunctions.push(func);
};

/**
 * Registers an event listener to be bound after component load
 * @param {string} type DOM event type
 * @param {Function} func handler function
 * @param {Element[]} elements elements to attach the listener to
 */
const registerEvent = (type, func, elements) => {
    updateEvents.push([type, func, elements]);
};

/**
 * Run function on input event to elements
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for input
 */
const OnInput = (func, elements) => {
    registerEvent("input", func, elements);
};

/**
 * Run function on change event to elements
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for change
 */
const OnChange = (func, elements) => {
    registerEvent("change", func, elements);
};

/**
 * Run function on submit event to elements
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for submit
 */
const OnSubmit = (func, elements) => {
    registerEvent("submit", func, elements);
};

/**
 * Run function on click event to elements
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for click
 */
const OnClick = (func, elements) => {
    registerEvent("click", func, elements);
};

/**
 * Run function on double click event to elements
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for double click
 */
const OnDblClick = (func, elements) => {
    registerEvent("dblclick", func, elements);
};

/**
 * Run function on mouse enter event to elements
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for mouse enter
 */
const OnMouseEnter = (func, elements) => {
    registerEvent("mouseenter", func, elements);
};

/**
 * Run function on mouse leave event to elements
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for mouse leave
 */
const OnMouseLeave = (func, elements) => {
    registerEvent("mouseleave", func, elements);
};

/**
 * Run function on keydown event to elements
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for keydown
 */
const OnKeyDown = (func, elements) => {
    registerEvent("keydown", func, elements);
};

/**
 * Run function on keyup event to elements
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for keyup
 */
const OnKeyUp = (func, elements) => {
    registerEvent("keyup", func, elements);
};

/**
 * Run function on focus event to elements
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for focus
 */
const OnFocus = (func, elements) => {
    registerEvent("focus", func, elements);
};

/**
 * Run function on blur event to elements
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for blur
 */
const OnBlur = (func, elements) => {
    registerEvent("blur", func, elements);
};

/**
 * Run function on load event
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for load
 */
const OnLoad = (func, elements) => {
    registerEvent("load", func, elements);
};

/**
 * Run function on resize event
 * @param {Function} func function to be run
 * @param {Element[]} elements elements to listen for resize
 */
const OnResize = (func, elements) => {
    registerEvent("resize", func, elements);
};

const GetElem = (query, resolve = false) => {
    if (resolve) {
        return document.querySelector(query);
    }
    return () => document.querySelector(query);
};

const GetElems = (query, resolve) => {
    if (resolve) {
        return document.querySelectorAll(query);
    }
    return () => document.querySelectorAll(query);
};

const resolveElement = (value) => {
    while (typeof value === "function") {
        value = value();
    }
    return value instanceof Element ? value : null;
};

ApplyComponentFill("root").then(() => {
    deferredFunctions.forEach((func) => {
        func();
    });

    updateEvents.forEach(([type, func, elements]) => {
        console.log();
        elements.forEach((elem) => {
            resolveElement(elem).addEventListener(type, func);
        });
    });
});
