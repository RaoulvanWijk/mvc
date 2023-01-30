class Component
{
    baseUrl = 'http://localhost:8080/'
    async init(type, id, el) {
        let data = await this.render(type, id, el.getAttribute('data-component-params'))
        el.innerHTML = await data.text();
    }

    async render(type, id, data = {}) {
        let res = await fetch(this.baseUrl + `api/component/${type}`, {
            method: "post",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify( data ),
        });
        return res;
    }
}


let components = document.querySelectorAll('[data-component-type]')
let compClass = new Component()
components.forEach(async (comp) => {
    let compType = comp.getAttribute('data-component-type')
    let compId = comp.id
    await compClass.init(compType, compId, comp)
})