// API utility functions

/**
 * Fetches data from the API endpoint
 * @param {string} endpoint - The API endpoint to fetch from
 * @returns {Promise<Object>} The response data
 */
export const fetchData = async (endpoint) => {
  try {
    const response = await fetch(`${process.env.REACT_APP_API_URL || 'http://localhost:3001/api'}${endpoint}`);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return await response.json();
  } catch (error) {
    console.error('Error fetching data:', error);
    throw error;
  }
};

/**
 * Posts data to the API endpoint
 * @param {string} endpoint - The API endpoint to post to
 * @param {Object} data - The data to send in the request body
 * @returns {Promise<Object>} The response data
 */
export const postData = async (endpoint, data) => {
  try {
    const response = await fetch(`${process.env.REACT_APP_API_URL || 'http://localhost:3001/api'}${endpoint}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return await response.json();
  } catch (error) {
    console.error('Error posting data:', error);
    throw error;
  }
};