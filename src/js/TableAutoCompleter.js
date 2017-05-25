const TableAutoCompleter = (prompt, tableNames, links) => {

  let offerActive = false

  const init = () => {
    listenToPrompt()
    listenToEnter()
  }

  const listenToEnter = () => {
    document.addEventListener('keyup', e => {
      if (e.keyCode === 13 && offerActive) {
        console.log(offerActive + '!!')
      }
    })
  }

  const listenToPrompt = () => {
    prompt.addEventListener('keyup', e => {
      const words = prompt.value.split(' ')
      const lastWord = words[words.length - 1]
      if (lastWord.length < 2) {
        offerActive = false
        return
      }
      const matches = tableNames.filter(tableName => {
        if (tableName.startsWith(lastWord)) {
          return true
        }
      })
      if (matches.length < 1) {
        offerActive = false
        console.log('No matches')
        return
      }
      const tableNameOnOffer = matches[0]
      offerActive = tableNameOnOffer
      links.forEach(link => {
        const method = link.getAttribute('href').split('#')[1] === tableNameOnOffer ? 'add' : 'remove'
        link.classList[method]('autocomplete-candidate')
      })
    })
  }

  init()
}

export default TableAutoCompleter
