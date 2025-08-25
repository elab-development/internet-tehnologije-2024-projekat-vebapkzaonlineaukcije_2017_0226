import React, { useState, useEffect } from "react";

const CountdownTimer = ({ targetDate }) => {
  const [timeLeft, setTimeLeft] = useState(new Date(targetDate) - new Date());

  useEffect(() => {
    if (timeLeft <= 0) return;

    const interval = setInterval(() => {
      setTimeLeft(new Date(targetDate) - new Date());
    }, 1000);

    return () => clearInterval(interval);
  }, [targetDate, timeLeft]);

  const formatTime = () => {
    if (timeLeft <= 0) {
      return "00h 00m 00s";
    }

    const seconds = Math.floor((timeLeft / 1000) % 60);
    const minutes = Math.floor((timeLeft / 1000 / 60) % 60);
    const hours = Math.floor((timeLeft / (1000 * 60 * 60)) % 24);
    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));

    return `${days}d ${hours.toString().padStart(2, "0")}h ${minutes
      .toString()
      .padStart(2, "0")}m ${seconds.toString().padStart(2, "0")}s`;
  };

  return (
    <div className="countdown-timer">
      <p>{formatTime()}</p>
    </div>
  );
};

export default CountdownTimer;
