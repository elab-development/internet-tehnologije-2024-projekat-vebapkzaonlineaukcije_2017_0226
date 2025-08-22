import React, { createContext, useState, useEffect } from "react";
import axios from "axios";

export const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [firstName, setFirstName] = useState("");
  const [lastName, setLastName] = useState("");
  const [userId, setUserId] = useState(null);
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
          setUserId(userData.id || null);
        } catch (e) {
          console.error(
            "Greška pri parsiranju korisničkih podataka iz localStorage-a:",
            e
          );
          setIsLoggedIn(false);
          setFirstName("");
          setLastName("");
          setUserId(null);
          localStorage.removeItem("authToken");
          localStorage.removeItem("userData");
        }
      } else {
        setIsLoggedIn(false);
        setFirstName("");
        setLastName("");
        setUserId(null);
      }
      setLoadingAuth(false);
    };

    checkAuthStatus();
  }, []);

  const login = (token, user) => {
    localStorage.setItem("authToken", token);
    localStorage.setItem("userData", JSON.stringify(user));
    setIsLoggedIn(true);
    setFirstName(user.ime || "");
    setLastName(user.prezime || "");
    setUserId(user.id || null);
  };

  const logout = async () => {
    try {
      const authToken = localStorage.getItem("authToken");
      if (authToken) {
        await axios.post(
          "http://localhost:8000/api/logout",
          {},
          {
            headers: {
              Authorization: `Bearer ${authToken}`,
            },
          }
        );
      }
    } catch (error) {
      console.error(
        "Greška pri odjavi sa servera:",
        error.response ? error.response.data : error.message
      );
    } finally {
      localStorage.removeItem("authToken");
      localStorage.removeItem("userData");
      setIsLoggedIn(false);
      setFirstName("");
      setLastName("");
      setUserId(null);
    }
  };

  const authContextValue = {
    isLoggedIn,
    firstName,
    lastName,
    userId,
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
