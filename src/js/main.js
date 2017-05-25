let form
let sqlPrompt
let favorites
let favoritesWrapper
let tablesSection
let resultsWrapper

const init = () => {
  if (document.body.classList.contains('logged-out')) {
    return
  }
  resultsWrapper = document.querySelector('.results-wrapper')
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
  listenToLegendLinks()
  formatResults()
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

const toggleFavorites = () => {
  const open = favoritesWrapper.classList.contains('open')
  if (open === true) {
    hideFavorites()
  } else {
    showFavorites()
  }
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
  let open = window.localStorage.getItem('tables-open') || 'true'
  open = open === 'true'
  if (open === true) {
    showTablesSection()
  } else {
    hideTablesSection()
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
    if (e.keyCode === 27) { // "ESC"
      focusOnSqlPrompt()
    }
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
  const open = document.body.classList.contains('barTogglerOpen')
  if (open) {
    hideTablesSection()
  } else {
    showTablesSection()
  }
}

const showTablesSection = () => {
  tablesSection.classList.add('open')
  document.body.classList.add('barTogglerOpen')
  window.localStorage.setItem('tables-open', true)
}

const hideTablesSection = () => {
  tablesSection.classList.remove('open')
  document.body.classList.remove('barTogglerOpen')
  window.localStorage.setItem('tables-open', false)
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

const listenToLegendLinks = () => {
  const links = document.querySelectorAll('.prompt-help > small')
  Array.from(links).forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault()
      const action = link.getAttribute('data-action')
      dispatchAction(action)
    })
  })
}

const dispatchAction = (action) => {
  switch (action) {
    case 'run-query':
      form.submit()
      break
    case 'toggle-tables':
      toggleTablesSection()
      break
    case 'toggle-favorites':
      toggleFavorites()
      break
    case 'save-query-to-favorites':
      addQueryToFavorites()
      showFavorites()
      break
    case 'delete-last-favorite':
      deleteLastFavorite()
      renderFavorites()
      break
    case 'focus-on-prompt':
      focusOnSqlPrompt()
      break
  }
}

const formatResults = () => {
  const pre = document.querySelector('.results-wrapper > pre')
  const resultJson = pre.innerText
  const results = JSON.parse(resultJson)
  const columns = getColumnsAsArray(results)
  console.log(columns)
  const table = document.createElement('table')
  table.classList.add('results-table')
  const thead = document.createElement('thead')
  const tbody = document.createElement('tbody')
  const firstRow = document.createElement('tr')
  columns.forEach(colName => {
    const th = document.createElement('th')
    th.innerText = colName
    firstRow.appendChild(th)
  })
  thead.appendChild(firstRow)
  table.appendChild(thead)
  results.forEach(row => {
    const tr = document.createElement('tr')
    columns.forEach(colName => {
      const td = document.createElement('td')
      td.innerText = row[colName] || '-'
      tr.appendChild(td)
    })
    tbody.appendChild(tr)
  })
  table.appendChild(tbody)

  // resultsWrapper.removeChild(pre)
  resultsWrapper.appendChild(table)
}

const getColumnsAsArray = (data) => {
  return Object.keys(data.reduce((result, obj) => {
    return Object.assign(result, obj)
  }, {}))
}

init()
