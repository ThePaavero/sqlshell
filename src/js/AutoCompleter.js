const AutoCompleter = (prompt, tableNames, links) => {

  let offerActive = false

  const init = () => {
    listenToPrompt()
    listenToEnter()
  }

  const listenToEnter = () => {
    document.addEventListener('keydown', e => {
      if (e.keyCode === 9 && offerActive) { // "Tab"
        e.preventDefault()
        injectOffer(offerActive)
      }
    })
  }

  const injectOffer = (tableName) => {
    const query = prompt.value
    prompt.value = query.replace(getLastWord(query), tableName) + ' '
  }

  const getLastWord = (str) => {
    const words = str.split(' ')
    return words[words.length - 1]
  }

  const listenToPrompt = () => {
    prompt.addEventListener('keyup', e => {
      const lastWord = getLastWord(prompt.value)

      if (lastWord.length < 2) {
        offerActive = false
        renderTableButtons()
        return
      }

      const matches = tableNames.filter(tableName => {
        if (tableName.startsWith(lastWord)) {
          return true
        }
      })

      if (matches.length < 1) {
        offerActive = false
        return
      } else {
        offerActive = matches[0]
      }

      renderTableButtons()
    })
  }

  const renderTableButtons = () => {
    links.forEach(link => {
      const method = link.getAttribute('href').split('#')[1] === offerActive ? 'add' : 'remove'
      link.classList[method]('autocomplete-candidate')
    })
  }

  init()
}

export default AutoCompleter
