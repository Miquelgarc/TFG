function deepClone(obj, hash = new WeakMap()) {
  // Manejo de valores primitivos y null
  if (obj === null || typeof obj !== 'object') {
    return obj;
  }

  // Evitar ciclos infinitos
  if (hash.has(obj)) {
    return hash.get(obj);
  }

  // Clonar fechas
  if (obj instanceof Date) {
    return new Date(obj);
  }

  // Clonar arrays
  if (Array.isArray(obj)) {
    const arr = [];
    hash.set(obj, arr);
    obj.forEach((item, i) => {
      arr[i] = deepClone(item, hash);
    });
    return arr;
  }

  // Clonar objetos normales
  const clonedObj = {};
  hash.set(obj, clonedObj);
  Object.keys(obj).forEach(key => {
    clonedObj[key] = deepClone(obj[key], hash);
  });
  return clonedObj;
}

export { deepClone }