const AutoCompleter = (prompt, tableNames, links) => {

  let offerActive = false

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
    listenToPrompt()
    listenToTab()
  }

  const listenToTab = () => {
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
        render()
        return
      }

      let commandHit = false

      // Commands come before tables names.
      commandsToOffer.forEach(command => {
        if (command.startsWith(lastWord)) {
          offerActive = command
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
        offerActive = false
        return
      } else {
        offerActive = matches[0]
      }

      render()
    })
  }

  const render = () => {
    renderTableButtons()
    renderActiveOffer()
  }

  const renderActiveOffer = () => {
    // @todo
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
