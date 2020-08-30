class Icon {
    content = null;
    title = ''
    author = ''
    slug = ''

    constructor(file) {
        this.file = file
    }

    /**
     * Getter for Icon's author
     */
    getAuthor = () => {
        if (this.author.length < 1) {
            let nodes = this.getNodes()
            this.author = nodes.getElementsByTagName('text')
            this.author = Array.from(this.author)
            this.author = this.author.map((text) => text.childNodes[0].nodeValue)
            if (this.author.length > 0) {
                this.author = this.author[0].replace('Created by ', '')
                return this.author
            }
            this.author = 'The Noun Project'
        }

        return this.author
    }

    /**
     * Getter for obtaining the SVG code
     * free from inline credit
     */
    getCleanVersion = () => {
        let creditReg = new RegExp(/<text(|\s+[^>]*)>(.*?)<\/text\s*>/g)
        // strip hard-coded credit
        this.content = this.content.replace(creditReg, '')

        // update SVG viewbox dimension
        this.content = this.updateSvgViewBox()
        return this.content
    }

    /**
     * Getter for file extension
     */
    getExtension = () => {
        return this.file.name.match(/\.[0-9a-z]+$/i)[0]
    }

    /**
     * Getter returing a file object
     */
    getFile = () => {
        return this.file
    }

    /**
     * Getter for filename
     */
    getFileName() {
        return this.file.name;
    }

    /**
     * Getter for Icon ID
     */
    getId() {
        const idReg = new RegExp(/\d{1,}/g)
        let matches = this.file.name.match(idReg)
        return matches[0]

    }

    /**
     * Inner utility for parsing and 
     * traversing SVG nodes
     */
    getNodes = () => {
        let parser = new DOMParser()
        let xmlNodes = parser.parseFromString(this.content, 'text/xml')
        return xmlNodes
    }

    /**
     * Get slugified ID
     */
    getSlug = () => {
        let fileReg = new RegExp(/(noun_|_\d{1,}.[0-9a-z]+)/g)
        this.slug = this.file.name.replace(fileReg, '')
        this.slug = this.slug.charAt(0).toUpperCase() + this.slug.slice(1) + this.getId()
        return this.slug
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim()
            .replace(/\s+/g, ' ')
            .replace(/[^\w-]+/g, '')
            .replace(/--+/g, '')
    }

    /**
     * Getter for Icon's title
     */
    getTitle = () => {
        if (this.title.length < 1) {
            let fileReg = new RegExp(/(noun_|_\d{1,}.[0-9a-z]+)/g)
            this.title = this.file.name.replace(fileReg, '')
            this.title = this.title.charAt(0).toUpperCase() + this.title.slice(1)
            this.title = `File:${this.title} (${this.getId()}) - The Noun Project${this.getExtension()}`
        }

        return this.title;

    }

    /**
     * Utility method for resizing viewBox and
     * harmonizing its dimension
     */
    updateSvgViewBox = () => {
        // harmonize viewBox size
        let viewBoxReg = new RegExp(/viewBox="(\d[. ]?){1,}"/g)
        let viewBox = this.content.match(viewBoxReg)[0]
        let viewBoxCorners = viewBox.match(new RegExp(/(\d[.]?){1,}/g))
        let viewBoxTop = viewBoxCorners[viewBoxCorners.length - 2]
        let newViewBox = `viewBox="0 0 ${viewBoxTop} ${viewBoxTop}"`

        // update viewBox in svg content
        this.content = this.content.replace(viewBoxReg, newViewBox)
        return this.content
    }

    /**
     * Getter for rendered wikicode
     * It's useful for populating
     * the icon's page on Commons
     */
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


}