const form = document.querySelector('.sql-form')
const sqlPrompt = form.querySelector('textarea')
const favorites = []
const favoritesWrapper = document.querySelector('.favorites-wrapper')

const init = () => {
  listenToSubmitKeyCombination()
  printTableButtons(window.sqlshellData.tables)
  listenToSidebarTogglerLinks()
  listenToSubmitTriggers()
  focusOnSqlPrompt()
  populateFavoritesFromDisk()
  toggleFavoritesFromDisk()
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
  window.localStorage.setItem('favorites', JSON.stringify(favorites))
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

const renderFavorites = () => {
  const favoritesReversed = favorites.reverse()
  favoritesWrapper.innerHTML = '<h3>Favorites</h3>'
  const orderedList = document.createElement('ol')
  favoritesReversed.map(query => {
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
}

const showFavorites = () => {
  window.localStorage.setItem('favorites-open', true)
  favoritesWrapper.classList.add('open')
}
const hideFavorites = () => {
  window.localStorage.setItem('favorites-open', false)
  favoritesWrapper.classList.remove('open')
  console.log('CLOSE FAVS')
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
    }
    else if (e.keyCode === 76 && ctrlDown) { // "L"
      e.preventDefault()
      if (favoritesWrapper.classList.contains('open')) {
        hideFavorites()
      } else {
        showFavorites()
      }
    }
  })
  document.addEventListener('keyup', e => {
    if (e.keyCode === 17) {
      ctrlDown = false
    }
  })
}

const listenToSidebarTogglerLinks = () => {
  const links = document.querySelectorAll('.bar-toggler')
  Array.from(links).forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault()
      const toToggle = e.currentTarget.parentNode
      link.classList.toggle('open')
      toToggle.classList.toggle('open')
      document.body.classList.toggle('barTogglerOpen')
      const open = toToggle.classList.contains('open') ? 'open' : ''
      const url = window.sqlshellData.baseUrl + '?ajax=1&action=SET_TABLES_BAR_OPEN_STATUS&status=' + open
      window.fetch(url, {
        credentials: 'same-origin'
      })
        .then(console.log)
        .catch(console.error)
    })
  })
}

init()
