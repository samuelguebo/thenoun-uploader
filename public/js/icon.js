class Icon {
    content = null;
    constructor(file) {
        this.file = file
    }

    /**
     * Getter for Icon's author
     */
    getAuthor = () => {
        let nodes = this.getNodes()
        let author = nodes.getElementsByTagName('text')
        author = Array.from(author)
        author = author.map((text) => text.childNodes[0].nodeValue)
        if (author.length > 0)
            return author[0].replace('Created by ', '')
        return 'The Noun Project'
    }

    /**
     * Getter for obtaining the SVG code
     * free from inline credit
     */
    getCleanVersion = () => {
        let creditReg = new RegExp(/<text(|\s+[^>]*)>(.*?)<\/text\s*>/g)
        // strip hard-coded credit
        let cleanedIcon = this.content.replace(creditReg, '')

        // update SVG viewbox dimension
        cleanedIcon = this.updateSvgViewBox()
        return cleanedIcon
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
        //TODO: Handle invalid id, etc
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
     * Getter for Icon's title
     */
    getTitle = () => {
        let fileReg = new RegExp(/(noun_|_\d{1,}.[0-9a-z]+)/g)
        let title = this.file.name.replace(fileReg, '')
        title = title.charAt(0).toUpperCase() + title.slice(1)
        return `File:${title} (${this.getId()}) - The Noun Project${this.getExtension()}`
    }

    /**
     * Utility method for resizing viewBox and
     * harmonizing its dimension
     */
    updateSvgViewBox = () => {
        // harmonize viewBox size
        let viewBoxReg = new RegExp(/viewBox="(\d[ ]?){1,}"/g)
        let viewBox = this.content.match(viewBoxReg)[0]
        let viewBoxCorners = viewBox.match(new RegExp(/\d{1,}/g))
        let viewBoxTop = viewBoxCorners[viewBoxCorners.length - 1]
        let newViewBox = `viewBox="0 0 ${viewBoxTop} ${viewBoxTop}"`

        // update viewBox in svg content
        this.content = this.content.replace(viewBoxRegex, newViewBox)
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