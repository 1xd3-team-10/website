const debug = true;

const componentCache = new Map();

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

        element.replaceWith(fragment);

        await ApplyComponentFillToNode(fragment);
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

ApplyComponentFill("root");
