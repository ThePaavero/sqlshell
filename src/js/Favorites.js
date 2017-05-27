const Favorites = (favorites, sqlPrompt, favoritesWrapper) => {

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

  const toggleFavorites = () => {
    if (favorites.length < 1) {
      return
    }
    const open = favoritesWrapper.classList.contains('open')
    if (open === true) {
      hideFavorites()
    } else {
      showFavorites()
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
        sqlPrompt.focus()
      })
    })
    favoritesWrapper.appendChild(orderedList)
    favorites = favorites.reverse()
  }

  const showFavorites = () => {
    if (favorites.length < 1) {
      return
    }
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

  return {
    addQueryToFavorites,
    populateFavoritesFromDisk,
    toggleFavorites,
    showFavorites,
    hideFavorites,
    deleteLastFavorite,
    saveFavoritesToDisk,
    toggleFavoritesFromDisk,
    renderFavorites
  }
}

export default Favorites
