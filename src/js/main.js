let form
let sqlPrompt
let favorites
let favoritesWrapper
let tablesSection

const init = () => {
  if (document.body.classList.contains('logged-out')) {
    return
  }
  form = document.querySelector('.sql-form')
  sqlPrompt = form.querySelector('textarea')
  favorites = []
  favoritesWrapper = document.querySelector('.favorites-wrapper')
  tablesSection = document.querySelector('.tables-section')

  listenToSubmitKeyCombination()
  printTableButtons(window.sqlshellData.tables)
  listenToSubmitTriggers()
  focusOnSqlPrompt()
  populateFavoritesFromDisk()
  toggleFavoritesFromDisk()
  toggleTablesFromDisk()
  listenToLogOutAndCloseLinks()
}

const listenToSubmitTriggers = () => {
  const buttons = document.querySelectorAll('.submit-on-click')
  Array.from(buttons).forEach(button => {
    button.addEventListener('click', e => {
      e.preventDefault()
      form.submit()
    })
  })
}

const focusOnSqlPrompt = () => {
  sqlPrompt.focus()
  sqlPrompt.setSelectionRange(sqlPrompt.value.length, sqlPrompt.value.length)
}

const activateTable = (tableName) => {
  sqlPrompt.innerText = 'select * from ' + tableName
  focusOnSqlPrompt()
}

const printTableButtons = (tables) => {
  const wrapper = document.querySelector('.tables')
  tables.map(row => {
    const myKey = Object.keys(row)[0]
    const tableName = row[myKey]
    const link = document.createElement('a')
    link.className = 'table-button'
    link.href = '#' + tableName
    link.innerText = tableName
    link.addEventListener('click', e => {
      e.preventDefault()
      activateTable(tableName)
    })
    wrapper.appendChild(link)
  })
}

const addQueryToFavorites = () => {
  const currentQuery = sqlPrompt.value
  favorites.push(currentQuery)
  saveFavoritesToDisk()
  renderFavorites()
}

const populateFavoritesFromDisk = () => {
  const fromDisk = window.localStorage.getItem('favorites')
  if (!fromDisk) {
    return
  }
  const diskFavorites = JSON.parse(fromDisk)
  diskFavorites.forEach(query => {
    favorites.push(query)
  })
  renderFavorites()
}

const toggleFavoritesFromDisk = () => {
  let open = window.localStorage.getItem('favorites-open') || false
  open = open === 'true'
  if (open === true) {
    showFavorites()
  } else {
    hideFavorites()
  }
}

const toggleTablesFromDisk = () => {
  let open = window.localStorage.getItem('tables-open') || false
  open = open === 'true'
  if (open === true) {

  } else {
    toggleTablesSection()
  }
}

const renderFavorites = () => {
  favorites = favorites.reverse()
  if (favorites.length < 1) {
    hideFavorites()
    return
  }
  favoritesWrapper.innerHTML = '<h3>Favorites</h3>'
  const orderedList = document.createElement('ol')
  favorites.map(query => {
    const link = document.createElement('li')
    link.setAttribute('data-query', query)
    link.innerText = query
    orderedList.appendChild(link)
    link.addEventListener('click', e => {
      e.preventDefault()
      sqlPrompt.value = link.getAttribute('data-query')
      focusOnSqlPrompt()
    })
  })
  favoritesWrapper.appendChild(orderedList)
  favorites = favorites.reverse()
}

const showFavorites = () => {
  window.localStorage.setItem('favorites-open', true)
  favoritesWrapper.classList.add('open')
}
const hideFavorites = () => {
  window.localStorage.setItem('favorites-open', false)
  favoritesWrapper.classList.remove('open')
}

const deleteLastFavorite = () => {
  favorites.shift()
  saveFavoritesToDisk()
}

const saveFavoritesToDisk = () => {
  window.localStorage.setItem('favorites', JSON.stringify(favorites))
}

const listenToSubmitKeyCombination = () => {
  let ctrlDown = false
  document.addEventListener('keydown', e => {
    if (e.keyCode === 17) {
      ctrlDown = true
    }
    else if (e.keyCode === 13 && ctrlDown) {
      form.submit()
    }
    else if (e.keyCode === 83 && ctrlDown) { // "S"
      e.preventDefault()
      addQueryToFavorites()
      showFavorites()
    }
    else if (e.keyCode === 66 && ctrlDown) { // "B"
      e.preventDefault()
      toggleTablesSection()
    }
    else if (e.keyCode === 76 && ctrlDown) { // "L"
      e.preventDefault()
      if (favoritesWrapper.classList.contains('open')) {
        hideFavorites()
      } else {
        showFavorites()
      }
    }
    else if (e.keyCode === 68 && ctrlDown) { // "D"
      e.preventDefault()
      deleteLastFavorite()
      renderFavorites()
    }
  })
  document.addEventListener('keyup', e => {
    if (e.keyCode === 17) {
      ctrlDown = false
    }
  })
}

const toggleTablesSection = () => {
  tablesSection.classList.toggle('open')
  document.body.classList.toggle('barTogglerOpen')
  const open = document.body.classList.contains('barTogglerOpen')
  window.localStorage.setItem('tables-open', open)
}

const logOutAndClose = () => {
  const url = window.sqlshellData.baseUrl + '?ajax=1&action=LOG_OUT'
  window.fetch(url, {
    credentials: 'same-origin'
  })
    .then(() => {
      window.location = window.location.href
    })
    .catch(console.error)
}

const listenToLogOutAndCloseLinks = () => {
  const links = document.querySelectorAll('.log-out')
  Array.from(links).forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault()
      logOutAndClose()
    })
  })
}

init()
