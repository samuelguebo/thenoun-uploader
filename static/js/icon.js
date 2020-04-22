class Icon {

    constructor(file) {
        this.file = file
        this.content = file.target.result
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
        author = author.map((text) => text.childNodes[0][0].nodeValue)
        return author
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

    getWikiCode = () => {
        let today = new Date()
        let fullYear = today.getFullYear()
        let month = today.getMonth() < 9 ? '0' + (today.getMonth() + 1) : (today.getMonth() + 1)
        let day = today.getDay() < 9 ? '0' + (today.getDay() + 1) : (today.getDay() + 1)
        let authorName = this.getAuthor()
        let authorLink = `https://thenounproject.com/icon/${iconId}`
        `=={{int:filedesc}}==
        {{Information
        |description={{en|1=${iconTitle} (${iconId}) - The Noun Project.svg}}
        |date= ${fullYear}-${month}-${day}
        |source=Noun Project - ${authorLink}
        |author= [${authorLink} ${authorName}] 
        |permission=
        |other versions=
        }}
        
        =={{int:license-header}}==
        {{Cc-by-sa-3.0}}
        {{The Noun Project}}
        `
    }


}

export default Icon;