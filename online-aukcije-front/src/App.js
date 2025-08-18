import logo from "./logo.svg";
import "./App.css";
import HomePage from "./components/HomePage";
import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import RegisterPage from "./components/RegisterPage";

function App() {
  return (
    <BrowserRouter className="App">
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/register" element={<RegisterPage />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
