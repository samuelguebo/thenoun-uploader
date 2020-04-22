class Icon {

    constructor(file) {
        this.file = file
        this.content = file.target.result
    }

    getCredits = () => {
        let nodes = this.getNodes()
        let credits = nodes.getElementsByTagName('text')

        credits = Array.from(credits)
        credits = credits.map((text) => text.childNodes[0].nodeValue)
        credits = credits.join(" ")

        let creditReg = new RegExp(/<text(|\s+[^>]*)>(.*?)<\/text\s*>/g)

        this.credits = credits
        return credits

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


}