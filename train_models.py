"""
Vietnam Real Estate Price Classification
=========================================
Train Decision Tree and Random Forest classifiers to predict price segments.
"""

import pandas as pd
import numpy as np
import joblib
import json
import warnings
from pathlib import Path
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.tree import DecisionTreeClassifier
from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.metrics import (
    accuracy_score, precision_score, recall_score, f1_score,
    classification_report, confusion_matrix, ConfusionMatrixDisplay
)
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt

warnings.filterwarnings('ignore')

# Paths
DATA_PATH = Path(__file__).parent / 'vietnam_housing_dataset.csv'
MODEL_DIR = Path(__file__).parent / 'models'
MODEL_DIR.mkdir(exist_ok=True)

print("=" * 60)
print("Vietnam Real Estate Price Classification Training")
print("=" * 60)

# ============================================================================
# 1. Load and Prepare Data
# ============================================================================
print("\n[1/7] Loading dataset...")

df = pd.read_csv(DATA_PATH)
print(f"    Total records: {len(df)}")

# Clean column names
df.columns = df.columns.str.strip()

# Standardize column names
column_mapping = {
    'Address': 'address',
    'Area': 'area',
    'Frontage': 'frontage',
    'Access Road': 'access_road',
    'House direction': 'house_direction',
    'Balcony direction': 'balcony_direction',
    'Floors': 'floors',
    'Bedrooms': 'bedrooms',
    'Bathrooms': 'bathrooms',
    'Legal status': 'legal_status',
    'Furniture state': 'furniture_state',
    'Price': 'price'
}
df = df.rename(columns=column_mapping)

print(f"    Columns: {list(df.columns)}")

# ============================================================================
# 2. Data Cleaning and Preprocessing
# ============================================================================
print("\n[2/7] Preprocessing data...")

# Remove rows with missing critical values
df_clean = df.dropna(subset=['address', 'area', 'price'])
df_clean = df_clean[df_clean['area'] > 0]
df_clean = df_clean[df_clean['price'] > 0]

print(f"    After cleaning: {len(df_clean)} records")

# Extract city from address
def extract_city(address):
    address = str(address).upper()
    cities = {
        'HÀ NỘI': 'Hà Nội', 'HANOI': 'Hà Nội',
        'HỒ CHÍ MINH': 'Hồ Chí Minh', 'HCM': 'Hồ Chí Minh',
        'ĐÀ NẴNG': 'Đà Nẵng',
        'HẢI PHÒNG': 'Hải Phòng',
        'CẦN THƠ': 'Cần Thơ',
        'HƯNG YÊN': 'Hưng Yên',
        'BÌNH DƯƠNG': 'Bình Dương',
        'ĐỒNG NAI': 'Đồng Nai',
        'QUẢNG NINH': 'Quảng Ninh',
        'PHÚ THỌ': 'Phú Thọ',
        'HẢI DƯƠNG': 'Hải Dương',
    }
    for key, value in cities.items():
        if key in address:
            return value
    return 'Other'

df_clean['city'] = df_clean['address'].apply(extract_city)

# Fill missing values
df_clean['frontage'] = pd.to_numeric(df_clean['frontage'], errors='coerce').fillna(0)
df_clean['access_road'] = pd.to_numeric(df_clean['access_road'], errors='coerce').fillna(0)
df_clean['floors'] = pd.to_numeric(df_clean['floors'], errors='coerce').fillna(1)
df_clean['bedrooms'] = pd.to_numeric(df_clean['bedrooms'], errors='coerce').fillna(1)
df_clean['bathrooms'] = pd.to_numeric(df_clean['bathrooms'], errors='coerce').fillna(1)

# Normalize categorical values
df_clean['legal_status'] = df_clean['legal_status'].fillna('Other')
df_clean['furniture_state'] = df_clean['furniture_state'].fillna('Basic')

def normalize_legal(status):
    status = str(status).strip().lower()
    if 'certificate' in status or 'sổ' in status or 'hồng' in status:
        return 'Have certificate'
    elif 'sale' in status or 'hợp đồng' in status:
        return 'Sale contract'
    elif 'pending' in status or 'chờ' in status:
        return 'Pending'
    return 'Other'

def normalize_furniture(status):
    status = str(status).strip().lower()
    if 'full' in status or 'đầy đủ' in status or 'cao cấp' in status:
        return 'Full'
    elif 'basic' in status or 'cơ bản' in status:
        return 'Basic'
    return 'Empty'

df_clean['legal_status'] = df_clean['legal_status'].apply(normalize_legal)
df_clean['furniture_state'] = df_clean['furniture_state'].apply(normalize_furniture)

# ============================================================================
# 3. Create Price Segments (Target Variable)
# ============================================================================
print("\n[3/7] Creating price segments...")

# Calculate percentiles for segmentation
percentile_33 = df_clean['price'].quantile(0.33)
percentile_67 = df_clean['price'].quantile(0.67)

print(f"    Low threshold (< {percentile_33:.2f})")
print(f"    Medium threshold (< {percentile_67:.2f})")
print(f"    High threshold (>= {percentile_67:.2f})")

def classify_price(price):
    if price < percentile_33:
        return 'Low Price'
    elif price < percentile_67:
        return 'Medium Price'
    else:
        return 'High Price'

df_clean['price_segment'] = df_clean['price'].apply(classify_price)

segment_counts = df_clean['price_segment'].value_counts()
for segment, count in segment_counts.items():
    print(f"    {segment}: {count} ({count/len(df_clean)*100:.1f}%)")

# ============================================================================
# 4. Feature Engineering
# ============================================================================
print("\n[4/7] Feature engineering...")

# One-Hot Encoding for categorical variables
df_encoded = pd.get_dummies(df_clean, columns=['legal_status', 'furniture_state', 'city'], 
                            prefix=['legal', 'furniture', 'city'])

# Select features
numeric_features = ['area', 'frontage', 'access_road', 'floors', 'bedrooms', 'bathrooms']
categorical_features = [col for col in df_encoded.columns if col.startswith(('legal_', 'furniture_', 'city_'))]

feature_names = numeric_features + categorical_features
print(f"    Total features: {len(feature_names)}")
print(f"    Numeric: {numeric_features}")
print(f"    Categorical: {len(categorical_features)} dims")

X = df_encoded[feature_names].copy()
y = df_encoded['price_segment']

# Handle any remaining NaN
X = X.fillna(0)

# Scale numeric features
scaler = StandardScaler()
X_scaled = X.copy()
X_scaled[numeric_features] = scaler.fit_transform(X[numeric_features])

# ============================================================================
# 5. Train Models
# ============================================================================
print("\n[5/7] Training models...")

# Split data
X_train, X_test, y_train, y_test = train_test_split(
    X_scaled, y, test_size=0.2, random_state=42, stratify=y
)

print(f"    Training set: {len(X_train)} samples")
print(f"    Test set: {len(X_test)} samples")

# Train Decision Tree
print("\n    Training Decision Tree...")
dt_model = DecisionTreeClassifier(
    max_depth=10,
    min_samples_split=5,
    min_samples_leaf=2,
    random_state=42,
    class_weight='balanced'
)
dt_model.fit(X_train, y_train)
dt_pred = dt_model.predict(X_test)

dt_accuracy = accuracy_score(y_test, dt_pred)
dt_precision = precision_score(y_test, dt_pred, average='weighted')
dt_recall = recall_score(y_test, dt_pred, average='weighted')
dt_f1 = f1_score(y_test, dt_pred, average='weighted')

print(f"    Decision Tree Accuracy: {dt_accuracy:.4f}")
print(f"    Decision Tree Precision: {dt_precision:.4f}")
print(f"    Decision Tree Recall: {dt_recall:.4f}")
print(f"    Decision Tree F1-Score: {dt_f1:.4f}")

# Cross-validation for Decision Tree
dt_cv_scores = cross_val_score(dt_model, X_scaled, y, cv=5)
print(f"    Decision Tree CV Score: {dt_cv_scores.mean():.4f} (+/- {dt_cv_scores.std()*2:.4f})")

# Train Random Forest
print("\n    Training Random Forest...")
rf_model = RandomForestClassifier(
    n_estimators=100,
    max_depth=15,
    min_samples_split=5,
    min_samples_leaf=2,
    random_state=42,
    class_weight='balanced',
    n_jobs=-1
)
rf_model.fit(X_train, y_train)
rf_pred = rf_model.predict(X_test)

rf_accuracy = accuracy_score(y_test, rf_pred)
rf_precision = precision_score(y_test, rf_pred, average='weighted')
rf_recall = recall_score(y_test, rf_pred, average='weighted')
rf_f1 = f1_score(y_test, rf_pred, average='weighted')

print(f"    Random Forest Accuracy: {rf_accuracy:.4f}")
print(f"    Random Forest Precision: {rf_precision:.4f}")
print(f"    Random Forest Recall: {rf_recall:.4f}")
print(f"    Random Forest F1-Score: {rf_f1:.4f}")

# Cross-validation for Random Forest
rf_cv_scores = cross_val_score(rf_model, X_scaled, y, cv=5)
print(f"    Random Forest CV Score: {rf_cv_scores.mean():.4f} (+/- {rf_cv_scores.std()*2:.4f})")

# Select best model
if rf_accuracy >= dt_accuracy:
    best_model = rf_model
    best_model_name = 'Random Forest'
    best_metrics = {
        'accuracy': rf_accuracy,
        'precision': rf_precision,
        'recall': rf_recall,
        'f1_score': rf_f1,
        'cv_score': rf_cv_scores.mean(),
        'cv_std': rf_cv_scores.std()
    }
else:
    best_model = dt_model
    best_model_name = 'Decision Tree'
    best_metrics = {
        'accuracy': dt_accuracy,
        'precision': dt_precision,
        'recall': dt_recall,
        'f1_score': dt_f1,
        'cv_score': dt_cv_scores.mean(),
        'cv_std': dt_cv_scores.std()
    }

print(f"\n    Best Model: {best_model_name}")

# ============================================================================
# 6. Save Models
# ============================================================================
print("\n[6/7] Saving models...")

# Save the best model
model_filename = best_model_name.lower().replace(" ", "_") + "_model.pkl"
joblib.dump(best_model, MODEL_DIR / model_filename)
print(f"    Saved: {model_filename}")

# Also save Random Forest as main model (for consistency)
joblib.dump(rf_model, MODEL_DIR / 'random_forest_model.pkl')
print(f"    Saved: random_forest_model.pkl")

# Save Decision Tree
joblib.dump(dt_model, MODEL_DIR / 'decision_tree_model.pkl')
print(f"    Saved: decision_tree_model.pkl")

# Save scaler
joblib.dump(scaler, MODEL_DIR / 'scaler.pkl')
print(f"    Saved: scaler.pkl")

# Save feature names
joblib.dump(feature_names, MODEL_DIR / 'feature_names.pkl')
print(f"    Saved: feature_names.pkl")

# Save segment thresholds
thresholds = {
    'low_threshold': float(percentile_33),
    'medium_threshold': float(percentile_67),
}
with open(MODEL_DIR / 'thresholds.json', 'w') as f:
    json.dump(thresholds, f, indent=2)
print(f"    Saved: thresholds.json")

# Save metrics
all_metrics = {
    'best_model': best_model_name,
    'best_accuracy': float(best_metrics['accuracy']),
    'best_precision': float(best_metrics['precision']),
    'best_recall': float(best_metrics['recall']),
    'best_f1_score': float(best_metrics['f1_score']),
    'best_cv_score': float(best_metrics['cv_score']),
    'best_cv_std': float(best_metrics['cv_std']),
    'decision_tree': {
        'accuracy': float(dt_accuracy),
        'precision': float(dt_precision),
        'recall': float(dt_recall),
        'f1_score': float(dt_f1),
        'cv_score': float(dt_cv_scores.mean()),
        'cv_std': float(dt_cv_scores.std())
    },
    'random_forest': {
        'accuracy': float(rf_accuracy),
        'precision': float(rf_precision),
        'recall': float(rf_recall),
        'f1_score': float(rf_f1),
        'cv_score': float(rf_cv_scores.mean()),
        'cv_std': float(rf_cv_scores.std())
    },
    'training_samples': int(len(X_train)),
    'test_samples': int(len(X_test)),
    'total_samples': int(len(df_clean)),
    'percentile_33': float(percentile_33),
    'percentile_67': float(percentile_67),
    'features': feature_names
}

with open(MODEL_DIR / 'metrics.json', 'w') as f:
    json.dump(all_metrics, f, indent=2)
print(f"    Saved: metrics.json")

# ============================================================================
# 7. Generate Visualization Reports
# ============================================================================
print("\n[7/7] Generating reports...")

# Classification Report
print("\n" + "=" * 60)
print("CLASSIFICATION REPORTS")
print("=" * 60)

print("\n--- Decision Tree ---")
print(classification_report(y_test, dt_pred))

print("\n--- Random Forest ---")
print(classification_report(y_test, rf_pred))

# Confusion Matrix Plot
fig, axes = plt.subplots(1, 2, figsize=(14, 5))

# Decision Tree Confusion Matrix
cm_dt = confusion_matrix(y_test, dt_pred, labels=['Low Price', 'Medium Price', 'High Price'])
disp_dt = ConfusionMatrixDisplay(confusion_matrix=cm_dt, display_labels=['Low', 'Medium', 'High'])
disp_dt.plot(ax=axes[0], cmap='Blues')
axes[0].set_title(f'Decision Tree\nAccuracy: {dt_accuracy:.3f}')

# Random Forest Confusion Matrix
cm_rf = confusion_matrix(y_test, rf_pred, labels=['Low Price', 'Medium Price', 'High Price'])
disp_rf = ConfusionMatrixDisplay(confusion_matrix=cm_rf, display_labels=['Low', 'Medium', 'High'])
disp_rf.plot(ax=axes[1], cmap='Greens')
axes[1].set_title(f'Random Forest\nAccuracy: {rf_accuracy:.3f}')

plt.tight_layout()
plt.savefig(MODEL_DIR / 'confusion_matrices.png', dpi=150, bbox_inches='tight')
plt.close()
print(f"    Saved: confusion_matrices.png")

# Feature Importance (Random Forest)
if hasattr(rf_model, 'feature_importances_'):
    importance_df = pd.DataFrame({
        'feature': feature_names,
        'importance': rf_model.feature_importances_
    }).sort_values('importance', ascending=False)
    
    fig, ax = plt.subplots(figsize=(10, 8))
    top_n = min(20, len(importance_df))
    ax.barh(range(top_n), importance_df['importance'].head(top_n).values)
    ax.set_yticks(range(top_n))
    ax.set_yticklabels(importance_df['feature'].head(top_n).values)
    ax.invert_yaxis()
    ax.set_xlabel('Feature Importance')
    ax.set_title('Random Forest Feature Importance (Top 20)')
    plt.tight_layout()
    plt.savefig(MODEL_DIR / 'feature_importance.png', dpi=150, bbox_inches='tight')
    plt.close()
    print(f"    Saved: feature_importance.png")
    
    # Save feature importance as CSV
    importance_df.to_csv(MODEL_DIR / 'feature_importance.csv', index=False)
    print(f"    Saved: feature_importance.csv")

# Model Comparison
fig, axes = plt.subplots(1, 2, figsize=(12, 5))

# Metrics comparison
metrics_names = ['Accuracy', 'Precision', 'Recall', 'F1-Score']
dt_values = [dt_accuracy, dt_precision, dt_recall, dt_f1]
rf_values = [rf_accuracy, rf_precision, rf_recall, rf_f1]

x = np.arange(len(metrics_names))
width = 0.35

bars1 = axes[0].bar(x - width/2, dt_values, width, label='Decision Tree', color='steelblue')
bars2 = axes[0].bar(x + width/2, rf_values, width, label='Random Forest', color='forestgreen')

axes[0].set_ylabel('Score')
axes[0].set_title('Model Comparison')
axes[0].set_xticks(x)
axes[0].set_xticklabels(metrics_names)
axes[0].legend()
axes[0].set_ylim(0, 1)
axes[0].axhline(y=0.8, color='r', linestyle='--', alpha=0.5, label='Baseline 0.8')

# Add value labels on bars
for bar in bars1:
    height = bar.get_height()
    axes[0].annotate(f'{height:.3f}',
                    xy=(bar.get_x() + bar.get_width() / 2, height),
                    xytext=(0, 3),
                    textcoords="offset points",
                    ha='center', va='bottom', fontsize=8)
for bar in bars2:
    height = bar.get_height()
    axes[0].annotate(f'{height:.3f}',
                    xy=(bar.get_x() + bar.get_width() / 2, height),
                    xytext=(0, 3),
                    textcoords="offset points",
                    ha='center', va='bottom', fontsize=8)

# Price segment distribution
segment_counts = df_clean['price_segment'].value_counts()
colors = {'Low Price': '#22c55e', 'Medium Price': '#f59e0b', 'High Price': '#ef4444'}
axes[1].pie(segment_counts.values, labels=segment_counts.index, autopct='%1.1f%%',
           colors=[colors[s] for s in segment_counts.index], startangle=90)
axes[1].set_title('Price Segment Distribution')

plt.tight_layout()
plt.savefig(MODEL_DIR / 'model_comparison.png', dpi=150, bbox_inches='tight')
plt.close()
print(f"    Saved: model_comparison.png")

print("\n" + "=" * 60)
print("TRAINING COMPLETE!")
print("=" * 60)
print(f"\nModels saved to: {MODEL_DIR}")
print(f"\nBest Model: {best_model_name}")
print(f"Best Accuracy: {best_metrics['accuracy']:.4f}")
print(f"Best F1-Score: {best_metrics['f1_score']:.4f}")
print("\nFiles created:")
for f in MODEL_DIR.iterdir():
    print(f"  - {f.name}")
