import React from "react";

const AuctionCard = ({ auction }) => {
  const {
    naziv,
    pocetna_cena,
    trenutna_cena,
    maksimalna_cena,
    datum_pocetka,
    vreme_isteka,
    status_aukcije,
    proizvodi,
  } = auction;

  console.log("Svi proizvodi:", proizvodi);
  if (proizvodi && proizvodi.length > 0) {
    console.log("Prvi proizvod:", proizvodi[0]);
    console.log("Vrednost slika_url:", proizvodi[0].slika_url);
  } else {
    console.log("Nema proizvoda u ovoj aukciji.");
  }

  const prvaSlika =
    proizvodi && proizvodi.length > 0
      ? proizvodi[0].slika_url
      : "https://via.placeholder.com/300x200";

  const datumPocetka = new Date(datum_pocetka);

  const godina = datumPocetka.getFullYear();
  const mesec = String(datumPocetka.getMonth() + 1).padStart(2, "0");
  const dan = String(datumPocetka.getDate()).padStart(2, "0");
  const sati = String(datumPocetka.getHours()).padStart(2, "0");
  const minuti = String(datumPocetka.getMinutes()).padStart(2, "0");
  const sekunde = String(datumPocetka.getSeconds()).padStart(2, "0");

  const formatiraniDatumPocetka = `${godina}.${mesec}.${dan} | ${sati}:${minuti}:${sekunde}`;

  return (
    <div className="auction-card">
      <div className="card-image-container">
        <img src={prvaSlika} alt={`Slika za aukciju: ${naziv}`} />
      </div>
      <div className="card-content">
        <h3 className="card-title">{naziv}</h3>
        <p>
          <strong>Pocetna cena:</strong> {pocetna_cena} RSD
        </p>
        <p>
          <strong>Pocetak aukcije</strong> {formatiraniDatumPocetka}
        </p>
        <button className="view-button">Pogledaj aukciju</button>
      </div>
    </div>
  );
};

export default AuctionCard;
