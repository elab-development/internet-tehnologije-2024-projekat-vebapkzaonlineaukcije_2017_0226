import React, { createContext, useState, useEffect } from "react";
import axios from "axios";

export const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [firstName, setFirstName] = useState("");
  const [lastName, setLastName] = useState("");
  const [loadingAuth, setLoadingAuth] = useState(true);

  useEffect(() => {
    const checkAuthStatus = () => {
      const authToken = localStorage.getItem("authToken");
      const userDataString = localStorage.getItem("userData");

      if (authToken && userDataString) {
        try {
          const userData = JSON.parse(userDataString);
          setIsLoggedIn(true);
          setFirstName(userData.ime || "");
          setLastName(userData.prezime || "");
          axios.defaults.headers.common[
            "Authorization"
          ] = `Bearer ${authToken}`;
        } catch (e) {
          console.error(
            "Greska pri parsiranju korisnickih podataka iz localStorage-a:",
            e
          );
          setIsLoggedIn(false);
          setFirstName("");
          setLastName("");
          localStorage.removeItem("authToken");
          localStorage.removeItem("userData");
        }
      } else {
        setIsLoggedIn(false);
        setFirstName("");
        setLastName("");
      }
      setLoadingAuth(false);
    };

    checkAuthStatus();
  }, []);

  const login = (token, user) => {
    localStorage.setItem("authToken", token);
    localStorage.setItem("userData", JSON.stringify(user));
    setIsLoggedIn(true);
    // Poboljsana provera da se spreci greska
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    if (user) {
      setFirstName(user.ime || "");
      setLastName(user.prezime || "");
    } else {
      // Resetuj stanje ako nema korisnickih podataka
      setFirstName("");
      setLastName("");
    }
  };

  const logout = async () => {
    try {
      const authToken = localStorage.getItem("authToken");
      if (authToken) {
        await axios.post("http://localhost:8000/api/logout");
      }
    } catch (error) {
      console.error(
        "Greska pri odjavi sa servera:",
        error.response ? error.response.data : error.message
      );
    } finally {
      localStorage.removeItem("authToken");
      localStorage.removeItem("userData");
      setIsLoggedIn(false);
      setFirstName("");
      setLastName("");

      delete axios.defaults.headers.common["Authorization"];
    }
  };

  const authContextValue = {
    isLoggedIn,
    firstName,
    lastName,
    login,
    logout,
    loadingAuth,
  };

  return (
    <AuthContext.Provider value={authContextValue}>
      {children}
    </AuthContext.Provider>
  );
};
