class Icon {
    content = null;
    constructor(file) {
        this.file = file
    }

    getFileName() {
        return this.file.name;
    }

    getId() {
        const idReg = new RegExp(/\d{5,}/)
        let matches = this.file.name.match(idReg)
        //TODO: Handle invalid id, etc
        return matches[0]

    }
    getAuthor = () => {
        let nodes = this.getNodes()
        let author = nodes.getElementsByTagName('text')
        author = Array.from(author)
        author = author.map((text) => text.childNodes[0].nodeValue)
        return author[0].replace('Created by ', '')
    }

    getCleanVersion = () => {
        let creditReg = new RegExp(/<text(|\s+[^>]*)>(.*?)<\/text\s*>/g)
        // strip hard-coded credit
        let cleanedIcon = this.content.replace(creditReg, '')
        return cleanedIcon
    }

    getNodes = () => {
        let parser = new DOMParser()
        let xmlNodes = parser.parseFromString(this.content, 'text/xml')
        return xmlNodes
    }

    getTitle = () => {
        //let fileReg = new RegExp(/(noun_|_\d{3,})/g)
        let fileReg = new RegExp(/(noun_|_\d{3,}.[0-9a-z]+)/g)
        let title = this.file.name.replace(fileReg, '')
        title = title.charAt(0).toUpperCase() + title.slice(1)
        return `File:${title} (${this.getId()}) - The Noun Project${this.getExtension()}`
    }

    getWikiCode = () => {
        let today = new Date()
        let fullYear = today.getFullYear()
        let month = today.getMonth() < 9 ? '0' + (today.getMonth() + 1) : (today.getMonth() + 1)
        let day = today.getDate() < 9 ? '0' + (today.getDate()) : (today.getDate())
        let iconId = this.getId()
        let iconTitle = this.getTitle()
        let authorName = this.getAuthor()
        let authorLink = `https://thenounproject.com/icon/${iconId}`
        let wikiCode = `=={{int:filedesc}}==\n`
        wikiCode += `{{Information\n`
        wikiCode += `|description={{en|1=${iconTitle}}}\n`
        wikiCode += `|date= ${fullYear}-${month}-${day}\n`
        wikiCode += `|source=Noun Project - ${authorLink}\n`
        wikiCode += `|author= [${authorLink} ${authorName}]\n`
        wikiCode += `|permission=\n`
        wikiCode += `|other versions=\n`
        wikiCode += `}}\n\n`

        wikiCode += `=={{int:license-header}}==\n`
        wikiCode += `{{Cc-by-sa-3.0}}\n`
        wikiCode += `{{The Noun Project}}`

        return wikiCode
    }

    getExtension = () => {
        return this.file.name.match(/\.[0-9a-z]+$/i)[0]
    }

    getFile = () => {
        return this.file
    }


}