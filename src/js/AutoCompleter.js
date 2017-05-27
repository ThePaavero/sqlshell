const AutoCompleter = (prompt, tableNames, links) => {

  let activeOffer = false
  let offerPreviewElement

  const commandsToOffer = [
    'select',
    'update',
    'insert',
    'count',
    'from',
    'order',
    'group by',
    'left join',
    'right join',
    'inner join',
    'outer join',
    'distinct',
    'create table',
    'delete',
    'where',
    'alter table',
    'add column',
    'limit'
  ]

  const init = () => {
    offerPreviewElement = document.querySelector('.autocomplete-offer-preview')
    listenToPrompt()
    listenToTab()
  }

  const listenToTab = () => {
    document.addEventListener('keydown', e => {
      if (e.keyCode === 9 && activeOffer) { // "Tab"
        e.preventDefault()
        injectOffer(activeOffer)
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
        activeOffer = false
        render()
        return
      }

      let commandHit = false

      // Commands come before tables names.
      commandsToOffer.forEach(command => {
        if (command.startsWith(lastWord)) {
          activeOffer = command
          render()
          commandHit = true
        }
      })
      if (commandHit) {
        // No point in continuing.
        return
      }

      // Ok, no command was matched, move on to tables.
      const matches = tableNames.filter(tableName => {
        if (tableName.startsWith(lastWord)) {
          return true
        }
      })

      if (matches.length < 1) {
        activeOffer = false
        return
      } else {
        activeOffer = matches[0]
      }

      render()
    })
  }

  const render = () => {
    renderTableButtons()
    renderActiveOffer()
  }

  const renderActiveOffer = () => {
    const offerString = activeOffer || ''
    offerPreviewElement.innerHTML = offerString
    const cssClassMethod = offerString ? 'add' : 'remove'
    offerPreviewElement.classList[cssClassMethod]('show')
  }

  const renderTableButtons = () => {
    links.forEach(link => {
      const method = link.getAttribute('href').split('#')[1] === activeOffer ? 'add' : 'remove'
      link.classList[method]('autocomplete-candidate')
    })
  }

  init()
}

export default AutoCompleter
