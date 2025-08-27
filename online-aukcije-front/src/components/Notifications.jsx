import React, { useState, useEffect, useContext } from "react";
import axios from "axios";
import { AuthContext } from "../context/AuthContext";

const Notifications = () => {
  const { isLoggedIn } = useContext(AuthContext);
  const [notifications, setNotifications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchNotifications = async () => {
      setLoading(true);
      setError(null);

      const authToken = localStorage.getItem("authToken");

      if (!authToken) {
        setLoading(false);
        return;
      }
      try {
        const response = await axios.get(
          "http://localhost:8000/api/notifications",
          {
            headers: {
              Authorization: `Bearer ${authToken}`,
            },
          }
        );

        setNotifications(response.data.data);
      } catch (err) {
        setError("Došlo je do greške prilikom učitavanja obaveštenja.");
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    if (isLoggedIn) {
      fetchNotifications();
    } else {
      setLoading(false);
    }
  }, [isLoggedIn]);

  if (loading) {
    return <div className="loading-message">Učitavanje obaveštenja...</div>;
  }

  if (error) {
    return <div className="error-message">{error}</div>;
  }

  if (!isLoggedIn) {
    return (
      <div className="notifications-container">
        <h2 className="notifications-title">
          Morate biti prijavljeni da biste videli obaveštenja.
        </h2>
      </div>
    );
  }

  return (
    <div className="notifications-container">
      <h2 className="notifications-title">Obaveštenja</h2>

      {notifications.length === 0 ? (
        <div className="no-notifications-message">
          Nemate novih obaveštenja.
        </div>
      ) : (
        <ul className="notifications-list">
          {notifications.map((notification) => (
            <li key={notification.id} className="notification-item">
              <div className="notification-header">
                <h5 className="notification-message">
                  {notification.data.poruka}
                </h5>
                <small>
                  {new Date(notification.created_at).toLocaleString()}
                </small>
              </div>

              {notification.data.kontakt_ime && (
                <div className="contact-details-container">
                  <p className="contact-details-title">Kontakt podaci:</p>
                  <ul className="contact-details-list">
                    <li>
                      Ime i prezime: {notification.data.kontakt_ime}{" "}
                      {notification.data.kontakt_prezime}
                    </li>
                    <li>Email: {notification.data.kontakt_email}</li>
                    <li>Telefon: {notification.data.kontakt_telefon}</li>
                  </ul>
                </div>
              )}
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default Notifications;
