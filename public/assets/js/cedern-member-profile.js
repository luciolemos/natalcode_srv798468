(() => {
  const form = document.querySelector('form.nc-member-profile-form')
    || document.querySelector('form[action$="/membro/perfil/completar"]');
  if (!form) {
    return;
  }

  const stateSelect = form.querySelector('[data-member-birth-state]');
  const citySelect = form.querySelector('[data-member-birth-city]');
  const photoInput = form.querySelector('#profile_photo');
  const phoneMobileInput = form.querySelector('#phone_mobile');
  const phoneLandlineInput = form.querySelector('#phone_landline');

  const cityCache = new Map();
  const requestTimeoutMs = 5000;
  const maxRetries = 2;

  const localCityFallbackByUf = {
    AC: ['Rio Branco'],
    AL: ['Maceió'],
    AP: ['Macapá'],
    AM: ['Manaus'],
    BA: ['Salvador'],
    CE: ['Fortaleza'],
    DF: ['Brasília'],
    ES: ['Vitória'],
    GO: ['Goiânia'],
    MA: ['São Luís'],
    MT: ['Cuiabá'],
    MS: ['Campo Grande'],
    MG: ['Belo Horizonte'],
    PA: ['Belém'],
    PB: ['João Pessoa'],
    PR: ['Curitiba'],
    PE: ['Recife'],
    PI: ['Teresina'],
    RJ: ['Rio de Janeiro'],
    RN: [
      'Acari', 'Afonso Bezerra', 'Água Nova', 'Alexandria',
      'Almino Afonso', 'Alto do Rodrigues', 'Angicos', 'Antônio Martins',
      'Apodi', 'Areia Branca', 'Arez', 'Assú',
      'Baía Formosa', 'Baraúna', 'Barcelona', 'Bento Fernandes',
      'Bodó', 'Bom Jesus', 'Brejinho', 'Caiçara do Norte',
      'Caiçara do Rio do Vento', 'Caicó', 'Campo Grande', 'Campo Redondo',
      'Canguaretama', 'Caraúbas', 'Carnaúba dos Dantas', 'Carnaubais',
      'Ceará-Mirim', 'Cerro Corá', 'Coronel Ezequiel', 'Coronel João Pessoa',
      'Cruzeta', 'Currais Novos', 'Doutor Severiano', 'Encanto',
      'Equador', 'Espírito Santo', 'Extremoz', 'Felipe Guerra',
      'Fernando Pedroza', 'Florânia', 'Francisco Dantas', 'Frutuoso Gomes',
      'Galinhos', 'Goianinha', 'Governador Dix-Sept Rosado', 'Grossos',
      'Guamaré', 'Ielmo Marinho', 'Ipanguaçu', 'Ipueira',
      'Itajá', 'Itaú', 'Jaçanã', 'Jandaíra',
      'Janduís', 'Januário Cicco', 'Japi', 'Jardim de Angicos',
      'Jardim de Piranhas', 'Jardim do Seridó', 'João Câmara', 'João Dias',
      'José da Penha', 'Jucurutu', 'Jundiá', 'Lagoa d\'Anta',
      'Lagoa de Pedras', 'Lagoa de Velhos', 'Lagoa Nova', 'Lagoa Salgada',
      'Lajes', 'Lajes Pintadas', 'Lucrécia', 'Luís Gomes',
      'Macaíba', 'Macau', 'Major Sales', 'Marcelino Vieira',
      'Martins', 'Maxaranguape', 'Messias Targino', 'Montanhas',
      'Monte Alegre', 'Monte das Gameleiras', 'Mossoró', 'Natal',
      'Nísia Floresta', 'Nova Cruz', 'Olho d\'Água do Borges', 'Ouro Branco',
      'Paraná', 'Paraú', 'Parazinho', 'Parelhas',
      'Parnamirim', 'Passa e Fica', 'Passagem', 'Patu',
      'Pau dos Ferros', 'Pedra Grande', 'Pedra Preta', 'Pedro Avelino',
      'Pedro Velho', 'Pendências', 'Pilões', 'Poço Branco',
      'Portalegre', 'Porto do Mangue', 'Pureza', 'Rafael Fernandes',
      'Rafael Godeiro', 'Riacho da Cruz', 'Riacho de Santana', 'Riachuelo',
      'Rio do Fogo', 'Rodolfo Fernandes', 'Ruy Barbosa', 'Santa Cruz',
      'Santa Maria', 'Santana do Matos', 'Santana do Seridó', 'Santo Antônio',
      'São Bento do Norte', 'São Bento do Trairí', 'São Fernando', 'São Francisco do Oeste',
      'São Gonçalo do Amarante', 'São João do Sabugi', 'São José de Mipibu', 'São José do Campestre',
      'São José do Seridó', 'São Miguel', 'São Miguel do Gostoso', 'São Paulo do Potengi',
      'São Pedro', 'São Rafael', 'São Tomé', 'São Vicente',
      'Senador Elói de Souza', 'Senador Georgino Avelino', 'Serra Caiada', 'Serra de São Bento',
      'Serra do Mel', 'Serra Negra do Norte', 'Serrinha', 'Serrinha dos Pintos',
      'Severiano Melo', 'Sítio Novo', 'Taboleiro Grande', 'Taipu',
      'Tangará', 'Tenente Ananias', 'Tenente Laurentino Cruz', 'Tibau',
      'Tibau do Sul', 'Timbaúba dos Batistas', 'Touros', 'Triunfo Potiguar',
      'Umarizal', 'Upanema', 'Várzea', 'Venha-Ver',
      'Vera Cruz', 'Viçosa', 'Vila Flor',
    ],
    RS: ['Porto Alegre'],
    RO: ['Porto Velho'],
    RR: ['Boa Vista'],
    SC: ['Florianópolis'],
    SP: ['São Paulo'],
    SE: ['Aracaju'],
    TO: ['Palmas'],
  };

  let cityStatusEl = null;

  const sanitizeDigits = (value) => (value || '').replace(/\D+/g, '');

  const formatMobilePhone = (value) => {
    const digits = sanitizeDigits(value).slice(0, 11);

    if (digits.length <= 2) {
      return digits;
    }

    if (digits.length <= 6) {
      return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
    }

    if (digits.length <= 10) {
      return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
    }

    return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
  };

  const formatLandlinePhone = (value) => {
    const digits = sanitizeDigits(value).slice(0, 10);

    if (digits.length <= 2) {
      return digits;
    }

    if (digits.length <= 6) {
      return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
    }

    return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6, 10)}`;
  };

  const applyPhoneMask = (input, formatter) => {
    if (!input) {
      return;
    }

    const onInput = () => {
      input.value = formatter(input.value);
    };

    input.addEventListener('input', onInput);
    onInput();
  };

  const clearCities = (placeholder = 'Selecione a cidade') => {
    if (!citySelect) {
      return;
    }

    citySelect.innerHTML = '';
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = placeholder;
    citySelect.appendChild(defaultOption);
  };

  const ensureCityStatus = () => {
    if (!citySelect) {
      return null;
    }

    if (cityStatusEl instanceof HTMLElement) {
      return cityStatusEl;
    }

    cityStatusEl = document.createElement('small');
    cityStatusEl.className = 'nc-member-profile-help';
    cityStatusEl.setAttribute('data-member-city-status', 'true');

    const parent = citySelect.parentElement;
    if (parent) {
      parent.appendChild(cityStatusEl);
    }

    return cityStatusEl;
  };

  const setCityStatus = (message = '') => {
    const status = ensureCityStatus();
    if (!status) {
      return;
    }

    status.textContent = message;
  };

  const populateCities = (cities, selectedCity = '') => {
    if (!citySelect) {
      return;
    }

    clearCities();

    cities.forEach((city) => {
      const option = document.createElement('option');
      option.value = city;
      option.textContent = city;
      if (selectedCity && city.toLowerCase() === selectedCity.toLowerCase()) {
        option.selected = true;
      }
      citySelect.appendChild(option);
    });

    citySelect.disabled = false;
  };

  const fetchWithTimeout = async (url, timeoutMs) => {
    const controller = new AbortController();
    const timeoutId = window.setTimeout(() => {
      controller.abort();
    }, timeoutMs);

    try {
      return await fetch(url, {
        method: 'GET',
        signal: controller.signal,
      });
    } finally {
      window.clearTimeout(timeoutId);
    }
  };

  const fetchCitiesByState = async (uf) => {
    if (!uf) {
      return [];
    }

    const normalizedUf = uf.toUpperCase();
    if (cityCache.has(normalizedUf)) {
      return cityCache.get(normalizedUf);
    }

    const endpoint = `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${normalizedUf}/municipios`;

    let lastError = null;
    for (let attempt = 1; attempt <= maxRetries; attempt += 1) {
      try {
        const response = await fetchWithTimeout(endpoint, requestTimeoutMs);

        if (!response.ok) {
          throw new Error('Resposta inválida da API de cidades.');
        }

        const data = await response.json();
        const cities = Array.isArray(data)
          ? data
              .map((item) => (item && typeof item.nome === 'string' ? item.nome : ''))
              .filter((name) => name !== '')
          : [];

        if (!cities.length) {
          throw new Error('Lista de cidades vazia.');
        }

        cityCache.set(normalizedUf, cities);
        return cities;
      } catch (error) {
        lastError = error;
      }
    }

    throw lastError || new Error('Não foi possível carregar cidades.');
  };

  const loadCities = async (uf, selectedCity = '') => {
    if (!citySelect) {
      return;
    }

    if (!uf) {
      citySelect.disabled = true;
      clearCities();
      return;
    }

    citySelect.disabled = true;
    clearCities('Carregando cidades...');
    setCityStatus('');

    try {
      const cities = await fetchCitiesByState(uf);
      populateCities(cities, selectedCity);
      setCityStatus('');
    } catch (error) {
      const fallbackCities = localCityFallbackByUf[(uf || '').toUpperCase()] || [];

      if (fallbackCities.length > 0) {
        populateCities(fallbackCities, selectedCity);
        setCityStatus('API indisponível no momento. Exibindo lista local temporária.');
      } else {
        citySelect.disabled = true;
        clearCities('Não foi possível carregar as cidades');
        setCityStatus('Falha ao consultar a API de cidades. Tente novamente.');
      }
    }
  };

  const initCityCascade = () => {
    if (!stateSelect || !citySelect) {
      return;
    }

    const selectedCityFromServer = citySelect.getAttribute('data-selected-city') || '';
    const initialUf = (stateSelect.value || '').trim();

    citySelect.disabled = true;
    clearCities();

    if (initialUf) {
      loadCities(initialUf, selectedCityFromServer);
    }

    stateSelect.addEventListener('change', () => {
      loadCities(stateSelect.value, '');
    });
  };

  const initPhotoPreview = () => {
    if (!photoInput) {
      return;
    }

    const previewWrap = form.querySelector('.nc-member-photo-preview-wrap');
    if (!previewWrap) {
      return;
    }

    const setPreview = (url) => {
      previewWrap.innerHTML = '';
      const image = document.createElement('img');
      image.className = 'nc-member-photo-preview';
      image.src = url;
      image.alt = 'Pré-visualização da foto de perfil';
      previewWrap.appendChild(image);
    };

    photoInput.addEventListener('change', () => {
      const file = photoInput.files && photoInput.files[0] ? photoInput.files[0] : null;
      if (!file) {
        return;
      }

      if (!file.type.startsWith('image/')) {
        return;
      }

      const objectUrl = URL.createObjectURL(file);
      setPreview(objectUrl);
    });
  };

  applyPhoneMask(phoneMobileInput, formatMobilePhone);
  applyPhoneMask(phoneLandlineInput, formatLandlinePhone);
  initCityCascade();
  initPhotoPreview();
})();
