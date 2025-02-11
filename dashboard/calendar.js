import React, { useState } from 'react';
import ReactDOM from 'react-dom';

function Calendar() {
  const [events, setEvents] = useState({});
  const [selectedDate, setSelectedDate] = useState(null);
  const [showModal, setShowModal] = useState(false);
  const [eventText, setEventText] = useState('');

  const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  const today = new Date();
  const year = today.getFullYear();
  const month = today.getMonth();

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  const calendarDays = Array.from({ length: firstDay }, () => null).concat(
    Array.from({ length: daysInMonth }, (_, i) => i + 1)
  );

  const handleDayClick = (day) => {
    setSelectedDate(day);
    setEventText(events[day] || '');
    setShowModal(true);
  };

  const handleSaveEvent = () => {
    setEvents({ ...events, [selectedDate]: eventText });
    setShowModal(false);
  };

  const handleDeleteEvent = () => {
    const newEvents = { ...events };
    delete newEvents[selectedDate];
    setEvents(newEvents);
    setShowModal(false);
  };

  return (
    <div className="max-w-md mx-auto p-4 border rounded-lg shadow-lg">
      <h2 className="text-xl font-bold text-center mb-4">
        {today.toLocaleString('default', { month: 'long' })} {year}
      </h2>
      <div className="grid grid-cols-7 gap-1 text-center font-medium">
        {days.map(day => (
          <div key={day} className="text-gray-600">{day}</div>
        ))}
        {calendarDays.map((day, index) => (
          <div
            key={index}
            className={`p-2 h-10 flex items-center justify-center rounded-lg cursor-pointer ${
              day === today.getDate() ? 'bg-blue-500 text-white' : 'bg-gray-100'
            }`}
            onClick={() => day && handleDayClick(day)}
          >
            {day || ''}
          </div>
        ))}
      </div>
      {showModal && (
        <div className="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
          <div className="bg-white p-4 rounded-lg shadow-lg w-80">
            <h3 className="text-lg font-bold mb-2">Schedule for {selectedDate}</h3>
            <textarea
              className="w-full p-2 border rounded"
              value={eventText}
              onChange={(e) => setEventText(e.target.value)}
            />
            <div className="flex justify-end mt-2 space-x-2">
              <button className="bg-green-500 text-white px-3 py-1 rounded" onClick={handleSaveEvent}>Save</button>
              <button className="bg-red-500 text-white px-3 py-1 rounded" onClick={handleDeleteEvent}>Delete</button>
              <button className="bg-gray-500 text-white px-3 py-1 rounded" onClick={() => setShowModal(false)}>Close</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

// Mount React Component
ReactDOM.render(<Calendar />, document.getElementById('calendar-root'));
